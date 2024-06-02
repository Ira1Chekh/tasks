<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        Task::factory(20)->create();
        Task::factory(10)->done()->create();

        User::factory(5)->create()->each(function ($user) {
            $tasks = Task::factory(3)->create(['user_id' => $user->id]);
            $tasks->each(function ($task) use ($user) {
                Task::factory(2)->create(['user_id' => $user->id, 'parent_id' => $task->id]);
            });
        });
    }
}
