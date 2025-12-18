<?php

namespace App\Http\Requests\Task;

use App\Http\Requests\ApiRequest;
use App\Models\Task;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['required', 'string', Rule::in(Task::priorityValues())],
            'status' => ['required', 'string', Rule::in(Task::statusValues())],
        ];
    }
}