<?php

namespace App\Http\Requests\StudySession;

use App\Http\Requests\ApiRequest;

class UpdateStudySessionRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'subject' => ['sometimes', 'nullable', 'string', 'max:255'],
            'date' => ['sometimes', 'required', 'date'],
            'start_time' => ['sometimes', 'nullable', 'date_format:H:i'],
            'end_time' => ['sometimes', 'nullable', 'date_format:H:i', 'after_or_equal:start_time'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ];
    }
}