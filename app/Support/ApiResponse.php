<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(mixed $data = null, string $message = '', int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message ?: 'Yeay, beres!',
        ], $status);
    }

    public static function created(mixed $data = null, string $message = 'Berhasil dibuat'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    public static function noContent(string $message = 'Beres.'): JsonResponse
    {
        return response()->json(['data' => null, 'message' => $message], 200);
    }

    /**
     * Format error terkait BusinessException (friendly message).
     */
    public static function error(string $message, int $status = 422, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'data' => null,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
