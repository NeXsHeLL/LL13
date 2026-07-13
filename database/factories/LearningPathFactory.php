<?php

namespace Database\Factories;

use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LearningPath>
 */
class LearningPathFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(3),
            'focus_area' => $this->faker->randomElement(['Laravel', 'Databases', 'Frontend', 'Testing', 'Architecture']),
            'outcome' => $this->faker->sentence(12),
            'status' => $this->faker->randomElement(['planned', 'active', 'paused', 'completed']),
            'weekly_minutes' => $this->faker->numberBetween(60, 480),
            'confidence' => $this->faker->numberBetween(1, 5),
            'target_date' => $this->faker->dateTimeBetween('+1 week', '+3 months')->format('Y-m-d'),
        ];
    }
}
