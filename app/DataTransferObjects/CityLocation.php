<?php

namespace App\DataTransferObjects;

// Localização de uma cidade devolvida pelo geocoding da Open Meteo.
readonly class CityLocation
{
    public function __construct(
        public string $name,
        public float $latitude,
        public float $longitude,
        public string $country,
        public string $countryCode,
        public ?string $admin1,
        public ?int $population = null,
    ) {}

    /**
     * Cria a partir do array JSON da Open Meteo.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromOpenMeteo(array $data): self
    {
        return new self(
            name: (string) $data['name'],
            latitude: (float) $data['latitude'],
            longitude: (float) $data['longitude'],
            country: (string) $data['country'],
            countryCode: (string) $data['country_code'],
            admin1: isset($data['admin1']) ? (string) $data['admin1'] : null,
            population: isset($data['population']) ? (int) $data['population'] : null,
        );
    }
}
