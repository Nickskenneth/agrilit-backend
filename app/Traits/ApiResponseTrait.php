<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function successResponse(
        mixed $data = null,
        string $message = 'Berhasil.',
        int $code = 200
    ): JsonResponse {
        $response = ['message' => $message];
        if (!is_null($data)) {
            $response['data'] = $data;
        }
        return response()->json($response, $code);
    }

    protected function errorResponse(
        string $message = 'Terjadi kesalahan.',
        int $code = 400,
        mixed $errors = null
    ): JsonResponse {
        $response = ['message' => $message];
        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }
        return response()->json($response, $code);
    }

    protected function paginatedResponse(
        mixed $paginator,
        string $message = 'Berhasil.'
    ): JsonResponse {
        return response()->json([
            'message' => $message,
            'data'    => $paginator->items(),
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }
}