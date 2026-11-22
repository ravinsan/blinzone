<?php

namespace App\Traits;

trait ApiResponseTrait
{
    /**
     * Success Response
     */
    protected function successResponse(
        $data = null,
        string $message = 'Success',
        int $code = 200
    ) {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Error Response
     */
    protected function errorResponse(
        string $message = 'Something went wrong',
        int $code = 400,
        $data = null
    ) {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Validation Response
     */
    protected function validationResponse(
        $errors,
        string $message = 'Validation failed'
    ) {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'errors'  => $errors,
        ], 422);
    }
}