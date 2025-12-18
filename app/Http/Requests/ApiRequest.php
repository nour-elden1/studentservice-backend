<?php

namespace App\Http\Requests;

use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class ApiRequest extends FormRequest
{
    use ApiResponse;

    protected function failedValidation(Validator $validator): void
    {
        $response = $this->errorResponse(
            'Validation failed',
            $validator->errors(),
            422
        );

        throw new HttpResponseException($response);
    }

    public function authorize(): bool
    {
        return true;
    }
}