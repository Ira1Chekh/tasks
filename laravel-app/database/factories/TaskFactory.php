<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'status' => TaskStatus::TODO->value,
            'user_id' => User::inRandomOrder()->first()->id,
            'priority' => $this->faker->numberBetween(1,5),
        ];
    }

    public function done()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => TaskStatus::DONE->value,
                'completed_at' => now(),
            ];
        });
    }
}
