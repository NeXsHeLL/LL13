<?php

namespace Database\Factories;

use App\Models\LearningLog;
use App\Models\LearningPath;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LearningLog>
 */
class LearningLogFactory extends Factory
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
            'learned_on' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'minutes' => $this->faker->numberBetween(20, 180),
            'topic' => $this->faker->sentence(4),
            'reflection' => $this->faker->paragraph(),
        ];
    }
}
