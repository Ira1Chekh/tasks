<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;
use App\Enums\TaskStatus;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function viewAny()
    {
        return true;
    }

    public function create() 
    {
        return true;
    }

    public function update(User $user, Task $task): bool
    {
        return $user->is($task->user);
    }

    public function view(User $user, Task $task): bool
    {
        return $user->is($task->user);
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->is($task->user) && $task->status != TaskStatus::DONE;
    }

    public function complete(User $user, Task $task): bool
    {
        return $user->is($task->user) && 
            ($task->subtasks()->doesntExist() 
            || $task->subtasks()->where('status', '!=', TaskStatus::DONE->value)->doesntExist());
    }
}
