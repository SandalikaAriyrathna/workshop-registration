<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ConcurrentRegistrationTest extends TestCase
{
    public function test_independent_processes_cannot_overbook_the_last_seat(): void
    {
        $database = tempnam(sys_get_temp_dir(), 'workshop-concurrency-');
        $barrier = $database.'.ready';
        $processes = [];
        $original = config('database.connections.sqlite.database');

        try {
            config(['database.connections.sqlite.database' => $database]);
            DB::purge('sqlite');
            $this->artisan('migrate', ['--database' => 'sqlite', '--force' => true])->assertExitCode(0);
            DB::connection('sqlite')->table('users')->insert([
                'id' => 1, 'name' => 'Staff', 'email' => 'staff@example.com',
                'password' => bcrypt('password'), 'role' => 'staff',
            ]);
            DB::connection('sqlite')->table('workshops')->insert([
                'id' => 1, 'code' => 'LAST-SEAT', 'title' => 'Last Seat', 'instructor' => 'Instructor',
                'starts_at' => now()->addDay(), 'capacity' => 1, 'status' => 'scheduled',
            ]);

            for ($index = 0; $index < 4; $index++) {
                $pipes = [];
                $process = proc_open([
                    PHP_BINARY, base_path('tests/Support/concurrent-registration.php'),
                    $database, $barrier, 'attendee'.$index,
                ], [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, base_path());
                $this->assertIsResource($process);
                fclose($pipes[0]);
                $processes[] = [$process, $pipes];
            }
            $deadline = microtime(true) + 10;
            do {
                $ready = 0;
                for ($index = 0; $index < 4; $index++) {
                    $ready += (int) file_exists($barrier.'.attendee'.$index);
                }
                if ($ready === 4) {
                    break;
                }
                usleep(1000);
            } while (microtime(true) < $deadline);
            $this->assertSame(4, $ready, 'All workers must reach the barrier before requests start.');
            touch($barrier);
            $results = [];
            foreach ($processes as [$process, $pipes]) {
                $results[] = trim(stream_get_contents($pipes[1]));
                $errors = stream_get_contents($pipes[2]);
                fclose($pipes[1]);
                fclose($pipes[2]);
                $this->assertSame(0, proc_close($process), $errors);
            }
            $processes = [];
            $this->assertSame(1, count(array_filter($results, fn ($result) => $result === 'registered')), implode("\n", $results));
            $this->assertSame(3, count(array_filter($results, fn ($result) => $result === 'rejected')));
            $this->assertSame(1, DB::connection('sqlite')->table('registrations')->where('status', 'active')->count());
        } finally {
            foreach ($processes as [$process, $pipes]) {
                if (is_resource($process)) {
                    proc_terminate($process);
                    foreach ($pipes as $pipe) {
                        if (is_resource($pipe)) {
                            fclose($pipe);
                        }
                    }
                    proc_close($process);
                }
            }
            DB::disconnect('sqlite');
            config(['database.connections.sqlite.database' => $original]);
            DB::purge('sqlite');
            foreach ([$barrier, $database, ...array_map(fn ($index) => $barrier.'.attendee'.$index, range(0, 3))] as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }
}
