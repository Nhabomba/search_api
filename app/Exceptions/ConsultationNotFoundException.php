<?php

namespace App\Exceptions;

use Exception;

class ConsultationNotFoundException extends Exception
{
    public static function withId(string $id): self
    {
        return new self("Consulta com id '{$id}' não encontrada.");
    }
}
