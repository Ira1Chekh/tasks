<?php

namespace App\Services;

use App\DTO\TaskDTO;
use App\Models\Task;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskListRequest;
use Illuminate\Support\Facades\Auth;
use App\Enums\TaskStatus;

class TaskService{

    public function createTask(TaskCreateRequest $request): TaskDTO
    {
        $dto = new TaskDTO(
                id: null,
                title: $request->get('title'),
                description: $request->get('description'),
                status: TaskStatus::TODO,
                priority: $request->get('priority'),
                user_id: $request->user()->id,
                completed_at: $request->get('completed_at'),
                parent_id: $request->get('parent_id')
            );
        $task = $dto->toModel();
        $task->save();

        return TaskDTO::fromModel($task);
    }

    public function updateTask(TaskCreateRequest $request, Task $task): TaskDTO
    {
        $dto = new TaskDTO(
            id: $task->id,
            title: $request->get('title'),
            description: $request->get('description'),
            status: $task->status,
            priority: $request->get('priority'),
            user_id: $request->user()->id,
            completed_at: $request->get('completed_at'),
            parent_id: $request->get('parent_id')
        );
        $task->update($dto->toModel()->toArray());

        return TaskDTO::fromModel($task);
    }


    public function listTask(TaskListRequest $request): array
    {
        $tasks = Task::where('user_id', $request->user()->id)
            ->whereNull('parent_id')
            ->when($request->get('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->get('priority'), function ($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when($request->get('search'), function ($query, $search) {
                $query->whereFullText(['title', 'description'], $search);
            })
            ->when($request->get('sort'), function ($query, $sort) {
                $sortArray = explode(',', $sort);
                foreach($sortArray as $sortItem) {
                    [$field, $direction] = explode(' ', trim($sortItem));
                    if (in_array($field, ['priority', 'created_at', 'completed_at']) && in_array(strtolower($direction), ['asc', 'desc'])) {
                        $query->orderBy($field, $direction);
                    }
                }
            })
            ->with('subtasks')
            ->get();

        $fetchSubtasks = function ($tasks) use (&$fetchSubtasks) {
            $result = [];
            foreach ($tasks as $task) {
                $subtasks = $fetchSubtasks($task->subtasks);
                $result[] = [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'priority' => $task->priority,
                    'status' => $task->status,
                    'completed_at' => $task->completed_at,
                    'subtasks' => $subtasks,
                ];
            }
            return $result;
        };

        return $fetchSubtasks($tasks);
    }

    public function showTask(Task $task): TaskDTO
    {
        return TaskDTO::fromModel($task);
    }

    public function completeTask(Task $task): TaskDTO
    {
        $dto = new TaskDTO(
            id: $task->id,
            title: $task->title,
            description: $task->description,
            status: TaskStatus::DONE,
            priority: $task->priority,
            user_id: $task->user_id,
            completed_at: date('Y-m-d H:i:s'),
            parent_id: $task->parent_id
        );
        $task->update($dto->toModel()->toArray());

        return TaskDTO::fromModel($task);
    }
}