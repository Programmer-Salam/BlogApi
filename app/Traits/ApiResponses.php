<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    protected function successResponse($data, $message = null, $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function errorResponse($message, $code): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null
        ], $code);
    }

    protected function resourceResponse($resource, $message = null, $code = 200): JsonResponse
    {
        return $this->successResponse($resource, $message, $code);
    }
}
