<?php

namespace App\Http\Requests\GroupResource;

use App\Http\Requests\ApiRequest;
use App\Models\GroupResource;
use Illuminate\Validation\Rule;

class StoreGroupResourceRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'type' => [
                'required',
                'string',
                Rule::in(GroupResource::typeValues()),
            ],

            'visibility' => [
                'nullable',
                'string',
                Rule::in(GroupResource::visibilityValues()),
            ],

            // For file resources
            'file' => [
                'required_if:type,file',
                'nullable',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,jpg,jpeg,png',
            ],

            // For link resources
            'link' => [
                'required_if:type,link',
                'nullable',
                'url',
                'max:2048',
            ],
        ];
    }
}