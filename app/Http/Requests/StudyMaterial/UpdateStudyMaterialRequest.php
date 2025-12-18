<?php

namespace App\Http\Requests\StudyMaterial;

use App\Http\Requests\ApiRequest;

class UpdateStudyMaterialRequest extends ApiRequest
{
    public function rules(): array
{
    return [
        'title' => ['sometimes', 'string', 'max:255'],
        'subject' => ['sometimes', 'string', 'nullable', 'max:255'],
        'description' => ['sometimes', 'string', 'nullable'],
        'file' => ['sometimes', 'file'], // updated
    ];
}

}