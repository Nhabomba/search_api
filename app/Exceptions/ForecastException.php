<?php

namespace App\Exceptions;

use Exception;

class ForecastException extends Exception
{
    public static function fromHttpException(OpenMeteoHttpException $exception): self
    {
        return new self(
            message: 'Falha ao consultar a API de previsão da Open Meteo: '.$exception->getMessage(),
            code: $exception->statusCode ?? 0,
        );
    }

    public static function invalidResponse(): self
    {
        return new self('Resposta inválida recebida da API de previsão.');
    }
}
