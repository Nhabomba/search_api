<?php

namespace App\DataTransferObjects;

// Clima atual devolvido pela API de forecast da Open Meteo.
readonly class WeatherForecast
{
    /**
     * @param  array<string, mixed>  $current
     * @param  array<string, string>  $currentUnits
     */
    public function __construct(
        public float $latitude,
        public float $longitude,
        public string $timezone,
        public array $current,
        public array $currentUnits,
    ) {}

    /**
     * Cria a partir do array JSON da Open Meteo.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromOpenMeteo(array $data): self
    {
        return new self(
            latitude: (float) $data['latitude'],
            longitude: (float) $data['longitude'],
            timezone: (string) $data['timezone'],
            current: (array) $data['current'],
            currentUnits: (array) $data['current_units'],
        );
    }
}
