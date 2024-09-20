<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseHelper
{
    protected function responseSuccess(array $data = [], string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $status,
        ], $status);
    }

    protected function responseError(array $data = [], string $message = 'Error', int $status = 400): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $status,
        ], $status);
    }

    protected function responseHiddenValidationError(array $errors): JsonResponse
    {
        $data['message'] = 'Unprocessable entity.';
        $data['status'] = 422;

        if (! app()->isProduction()) {
            $data['message'] = 'Development validation error, missing hidden parameters';
            $data['errors'] = $errors;
        }

        return response()->json($data, 422);
    }
}
