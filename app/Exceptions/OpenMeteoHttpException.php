<?php

namespace App\Exceptions;

use Exception;

class OpenMeteoHttpException extends Exception
{
    public function __construct(
        string $message,
        public readonly string $result = 'error',
        public readonly ?int $statusCode = null,
    ) {
        parent::__construct($message, $statusCode ?? 0);
    }

    public static function httpError(string $service, int $statusCode, string $result): self
    {
        return new self(
            message: "A API Open Meteo ({$service}) respondeu com HTTP {$statusCode}.",
            result: $result,
            statusCode: $statusCode,
        );
    }

    public static function emptyResponse(int $statusCode): self
    {
        return new self(
            message: 'A API Open Meteo devolveu uma resposta vazia.',
            result: 'empty_response',
            statusCode: $statusCode,
        );
    }

    public static function invalidJson(int $statusCode): self
    {
        return new self(
            message: 'A API Open Meteo devolveu JSON inválido.',
            result: 'invalid_json',
            statusCode: $statusCode,
        );
    }

    public static function apiError(string $reason, int $statusCode): self
    {
        return new self(
            message: $reason,
            result: 'api_error',
            statusCode: $statusCode,
        );
    }
}
