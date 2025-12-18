<?php

namespace App\Http\Requests\StudyMaterial;

use App\Http\Requests\ApiRequest;

class StoreStudyMaterialRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'file' => [
                'required',
                'file',
                'max:10240', // 10MB
                'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,jpg,jpeg,png',
            ],
        ];
    }
}