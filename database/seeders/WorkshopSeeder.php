<?php

namespace Database\Seeders;

use App\Models\Workshop;
use Illuminate\Database\Seeder;

class WorkshopSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Pottery Basics', 'Introduction to Coding', 'Community Fitness'] as $index => $title) {
            Workshop::firstOrCreate(['code' => 'DEMO-'.($index + 1)], [
                'title' => $title,
                'instructor' => ['Alex Silva', 'Sam Perera', 'Jamie Fernando'][$index],
                'starts_at' => now()->addDays($index + 2)->setTime(10, 0),
                'capacity' => [12, 20, 15][$index],
                'status' => 'scheduled',
                'location' => 'Training Centre '.($index + 1),
                'description' => 'A beginner-friendly community workshop.',
            ]);
        }
    }
}
