<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

// Formato padrão das respostas de erro da API.
class ApiErrorResponse
{
    /**
     * @param  array<string, array<int, string>>|null  $errors
     */
    public static function make(
        string $message,
        int $statusCode,
        ?string $requestId = null,
        ?array $errors = null,
    ): JsonResponse {
        $payload = ['message' => $message];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        if ($requestId !== null) {
            $payload['requestId'] = $requestId;
        }

        return response()->json($payload, $statusCode);
    }

    public static function internalServerError(?string $requestId = null): JsonResponse
    {
        return self::make('Ocorreu um erro interno no servidor.', 500, $requestId);
    }
}
