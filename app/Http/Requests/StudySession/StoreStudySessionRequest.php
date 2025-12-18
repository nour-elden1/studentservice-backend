<?php

namespace App\Http\Requests\StudySession;

use App\Http\Requests\ApiRequest;

class StoreStudySessionRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after_or_equal:start_time'],
            'notes' => ['nullable', 'string'],
        ];
    }
}