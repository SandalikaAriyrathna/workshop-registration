<?php

// Separate process to exercise the real controller and an independent DB connection.
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app['config']->set('database.default', 'sqlite');
$app['config']->set('database.connections.sqlite.database', $argv[1]);
Illuminate\Support\Facades\DB::purge();
Illuminate\Support\Facades\Auth::loginUsingId(1);
touch($argv[2].'.'.$argv[3]);

$deadline = microtime(true) + 10;
while (! file_exists($argv[2])) {
    if (microtime(true) > $deadline) {
        throw new RuntimeException('Concurrency barrier timed out.');
    }
    usleep(1000);
}

try {
    $request = Illuminate\Http\Request::create('/registrations', 'POST', [
        'workshop_id' => 1,
        'attendee_name' => 'Concurrent Attendee',
        'attendee_email' => $argv[3].'@example.com',
    ]);
    (new App\Http\Controllers\RegistrationController)->store($request);
    echo 'registered';
} catch (Illuminate\Validation\ValidationException $exception) {
    if (! isset($exception->errors()['workshop_id'])) {
        throw $exception;
    }
    echo 'rejected';
}
