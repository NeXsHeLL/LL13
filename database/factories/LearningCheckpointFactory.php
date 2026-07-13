<?php

namespace Database\Factories;

use App\Models\LearningCheckpoint;
use App\Models\LearningPath;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LearningCheckpoint>
 */
class LearningCheckpointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'learning_path_id' => LearningPath::factory(),
            'title' => $this->faker->sentence(4),
            'notes' => $this->faker->optional()->sentence(10),
            'activity_type' => 'task',
            'difficulty' => $this->faker->randomElement(['basics', 'intermediate', 'advanced']),
            'prompt' => $this->faker->sentence(10),
            'options' => null,
            'correct_option' => null,
            'user_answer' => null,
            'explanation' => null,
            'is_complete' => $this->faker->boolean(35),
            'position' => $this->faker->numberBetween(1, 10),
        ];
    }

    public function mcq(): static
    {
        return $this->state(fn (array $attributes) => [
            'activity_type' => 'mcq',
            'options' => [
                ['key' => 'A', 'text' => 'A route maps an HTTP request to code.'],
                ['key' => 'B', 'text' => 'A route stores uploaded files.'],
                ['key' => 'C', 'text' => 'A route compiles frontend assets.'],
                ['key' => 'D', 'text' => 'A route runs migrations.'],
            ],
            'correct_option' => 'A',
            'is_complete' => false,
        ]);
    }
}
