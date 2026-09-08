<?php

namespace App\Exceptions;

use Exception;

class GeocodingException extends Exception
{
    public static function fromHttpException(OpenMeteoHttpException $exception): self
    {
        return new self(
            message: 'Falha ao consultar a API de geocoding da Open Meteo: '.$exception->getMessage(),
            code: $exception->statusCode ?? 0,
        );
    }
}
