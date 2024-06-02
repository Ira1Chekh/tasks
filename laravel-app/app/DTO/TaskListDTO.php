<?php

namespace App\DTO;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Http\Requests\TaskListRequest;

class TaskListDTO
{
    public ?string $search;
    public ?string $sort;
    public ?string $status;
    public ?int $priority;

    public function __construct(TaskListRequest $request)
    {
        $this->search = $request->input('search');
        $this->sort = $request->input('sort');
        $this->status = $request->input('status');
        $this->priority = $request->input('priority');
    }
}