<?php

namespace App\Services;

use App\DTO\TaskDTO;
use App\DTO\TaskListDTO;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use App\Enums\TaskStatus;
use App\Http\Resources\TaskResource;

class TaskService{

    public function createTask(TaskDTO $dto): TaskResource
    {
        $task = $dto->toModel();
        $task->save();

        return TaskResource::make($task);
    }

    public function updateTask(TaskDTO $dto, Task $task): TaskResource
    {
        $task->update($dto->toModel()->toArray());

        return TaskResource::make($task);
    }

    public function listTask(TaskListDTO $dto): array
    {
        $tasks = Task::where('user_id', Auth::id())
            ->whereNull('parent_id')
            ->when($dto->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($dto->priority, function ($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when($dto->search, function ($query, $search) {
                $query->whereFullText(['title', 'description'], $search);
            })
            ->when($dto->sort, function ($query, $sort) {
                $this->applySorting($query, $sort);
            })
            ->with('subtasks')
            ->get();

        return $this->fetchSubtasks($tasks);
    }

    private function applySorting($query, string $sort): void
    {
        $sortArray = explode(',', $sort);
        foreach ($sortArray as $sortItem) {
            [$field, $direction] = explode(' ', trim($sortItem));
            $field = strtolower($field);
            $direction = strtolower($direction);

            if (in_array($field, ['priority', 'created_at', 'completed_at']) &&
                in_array($direction, ['asc', 'desc'])) {
                $query->orderBy($field, $direction);
            }
        }
    }

    private function fetchSubtasks($tasks): array
    {
        $result = [];
        foreach ($tasks as $task) {
            $subtasks = $this->fetchSubtasks($task->subtasks);
            $taskItem = $task->toArray();
            $taskItem['subtasks'] = $subtasks;
            $result[] = $taskItem;
        }
        return $result;
    }

    public function showTask(Task $task): TaskResource
    {
        return TaskResource::make($task->load(['user', 'subtasks']));
    }

    public function completeTask(TaskDTO $dto, Task $task): TaskResource
    {
        $task->update($dto->toModel()->toArray());

        return TaskResource::make($task);
    }
}
