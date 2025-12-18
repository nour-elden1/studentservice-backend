<?php

namespace App\Http\Requests\StudyGroup;

use App\Http\Requests\ApiRequest;

class StoreStudyGroupRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}