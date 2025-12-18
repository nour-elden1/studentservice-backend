<?php

namespace App\Http\Requests\GroupResource;

use App\Http\Requests\ApiRequest;
use App\Models\GroupResource;
use Illuminate\Validation\Rule;

class UpdateGroupResourceRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],

            'type' => [
                'sometimes',
                'required',
                'string',
                Rule::in(GroupResource::typeValues()),
            ],

            'visibility' => [
                'sometimes',
                'required',
                'string',
                Rule::in(GroupResource::visibilityValues()),
            ],

            'file' => [
                'sometimes',
                'nullable',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,jpg,jpeg,png',
            ],

            'link' => [
                'sometimes',
                'nullable',
                'url',
                'max:2048',
            ],
        ];
    }
}