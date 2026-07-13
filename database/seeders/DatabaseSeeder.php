<?php

namespace Database\Seeders;

use App\LearningQuestFactory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $blueprint = app(LearningQuestFactory::class)->laravelFullStack();
        $path = $user->learningPaths()->create($blueprint['path']);

        foreach ($blueprint['checkpoints'] as $index => $checkpoint) {
            $path->checkpoints()->create([
                ...$checkpoint,
                'position' => $index + 1,
            ]);
        }

        $path->logs()->createMany([
            [
                'learned_on' => now()->subDays(2)->toDateString(),
                'minutes' => 45,
                'topic' => 'Routes and controllers',
                'reflection' => 'I can explain how a route points to a controller action.',
            ],
            [
                'learned_on' => now()->subDay()->toDateString(),
                'minutes' => 60,
                'topic' => 'Eloquent relationships',
                'reflection' => 'Foreign keys and relationship methods make nested data much easier to query.',
            ],
        ]);
    }
}
