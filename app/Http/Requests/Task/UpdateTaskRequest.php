<?php

namespace App\Http\Requests\Task;

use App\Http\Requests\ApiRequest;
use App\Models\Task;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'priority' => ['sometimes', 'required', 'string', Rule::in(Task::priorityValues())],
            'status' => ['sometimes', 'required', 'string', Rule::in(Task::statusValues())],
        ];
    }
}