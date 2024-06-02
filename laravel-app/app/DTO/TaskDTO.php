<?php

namespace App\DTO;

use App\Models\Task;
use App\Enums\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskDTO
{
    public function __construct(
        public ?int $id,
        public string $title,
        public string $description,
        public TaskStatus $status,
        public int $priority,
        public int $user_id,
        public ?string $completed_at,
        public ?int $parent_id
    ) {}

    public static function fromModel(Task $task): self
    {
        return new self(
            id: $task->id,
            title: $task->title,
            description: $task->description,
            status: $task->status,
            priority: $task->priority,
            user_id: $task->user_id,
            completed_at: $task->completed_at,
            parent_id: $task->parent_id
        );
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            id: null,
            title: $request->get('title'),
            description: $request->get('description'),
            status: TaskStatus::from($request->get('status')),
            priority: $request->get('priority'),
            user_id: $request->user()->id,
            completed_at: $request->get('completed_at'),
            parent_id: $request->get('parent_id')
        );
    }

    public function toModel(): Task
    {
        return new Task([
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'user_id' => $this->user_id,
            'completed_at' => $this->completed_at,
            'parent_id' => $this->parent_id
        ]);
    }
}
