<?php

namespace App\Http\Requests\StudyGroup;

use App\Http\Requests\ApiRequest;

class UpdateStudyGroupRequest extends ApiRequest
{
    public function rules(): array
    {
            return [
                'name' => ['sometimes', 'required', 'string', 'max:255'],
                'description' => ['sometimes', 'nullable', 'string'],
            ];
    }
}