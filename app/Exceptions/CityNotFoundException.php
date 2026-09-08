<?php

namespace App\Exceptions;

use Exception;

class CityNotFoundException extends Exception
{
    public static function forCity(string $city, ?string $country = null): self
    {
        if ($country !== null) {
            return new self("Cidade '{$city}' não encontrada em '{$country}'.");
        }

        return new self("Cidade '{$city}' não encontrada.");
    }

    public static function countryMismatch(string $city, string $requestedCountry, string $actualCountry): self
    {
        return new self(
            "Cidade '{$city}' não encontrada em '{$requestedCountry}'. A localização encontrada pertence a '{$actualCountry}'."
        );
    }
}
