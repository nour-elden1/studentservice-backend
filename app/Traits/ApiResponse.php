<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function successResponse(
        string $message = 'Success',
        mixed $data = null,
        int $statusCode = 200
    ): JsonResponse {
        return response()->json([
            'success' => [
                'message' => $message,
                'data' => $data,
            ],
            'error' => null,
        ], $statusCode);
    }

    protected function errorResponse(
        string $message = 'Error',
        mixed $errors = null,
        int $statusCode = 400
    ): JsonResponse {
        return response()->json([
            'success' => null,
            'error' => [
                'message' => $message,
                'errors' => $errors,
            ],
        ], $statusCode);
    }
}