<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\TaskStatus;

class TaskListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['string'],
            'sort' => ['string'],
            'status' => ['string', Rule::in(TaskStatus::cases())],
            'priority' => 'integer|min:1|max:5',
        ];
    }
    
}