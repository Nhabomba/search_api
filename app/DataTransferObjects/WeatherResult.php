<?php

namespace App\DataTransferObjects;

// Resultado agregado de uma consulta (pedido + localização + clima).
readonly class WeatherResult
{
    public function __construct(
        public string $id,
        public string $requestedCity,
        public ?string $requestedCountry,
        public CityLocation $city,
        public WeatherForecast $forecast,
        public string $weatherTime,
        public string $consultedAt,
    ) {}
}
