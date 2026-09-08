<?php

namespace Tests\Support;

use App\DataTransferObjects\CityLocation;
use App\DataTransferObjects\WeatherForecast;

class FakeWeatherData
{
    /**
     * @return array<string, mixed>
     */
    public static function geocodingApiResponse(): array
    {
        return [
            'results' => [
                [
                    'id' => 1040652,
                    'name' => 'Beira',
                    'latitude' => -19.789104,
                    'longitude' => 34.81555,
                    'country_code' => 'MZ',
                    'timezone' => 'Africa/Maputo',
                    'country' => 'Moçambique',
                    'admin1' => 'Província de Sofala',
                    'population' => 530000,
                ],
            ],
        ];
    }

    /**
     * Localidade homónima pequena (ex.: Pretoria em Moçambique).
     *
     * @return array<string, mixed>
     */
    public static function pretoriaMozambiqueGeocodingApiResponse(): array
    {
        return [
            'results' => [
                [
                    'id' => 999001,
                    'name' => 'Pretoria',
                    'latitude' => -25.36722,
                    'longitude' => 32.95944,
                    'country_code' => 'MZ',
                    'timezone' => 'Africa/Maputo',
                    'country' => 'Moçambique',
                    'admin1' => 'Província de Maputo',
                    'population' => 120,
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function pretoriaGeocodingApiResponse(): array
    {
        return [
            'results' => [
                [
                    'id' => 964137,
                    'name' => 'Pretoria',
                    'latitude' => -25.747868,
                    'longitude' => 28.229271,
                    'country_code' => 'ZA',
                    'timezone' => 'Africa/Johannesburg',
                    'country' => 'South Africa',
                    'admin1' => 'Gauteng',
                    'population' => 741651,
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function forecastApiResponse(): array
    {
        return [
            'latitude' => -19.789104,
            'longitude' => 34.81555,
            'timezone' => 'Africa/Maputo',
            'current' => [
                'time' => '2026-09-08T12:45',
                'temperature_2m' => 23.9,
                'relative_humidity_2m' => 64,
                'apparent_temperature' => 23.0,
                'weather_code' => 2,
                'wind_speed_10m' => 25.3,
            ],
            'current_units' => [
                'temperature_2m' => '°C',
                'relative_humidity_2m' => '%',
                'apparent_temperature' => '°C',
                'weather_code' => 'wmo code',
                'wind_speed_10m' => 'km/h',
            ],
        ];
    }

    public static function cityLocation(): CityLocation
    {
        return CityLocation::fromOpenMeteo(self::geocodingApiResponse()['results'][0]);
    }

    public static function pretoriaLocation(): CityLocation
    {
        return CityLocation::fromOpenMeteo(self::pretoriaGeocodingApiResponse()['results'][0]);
    }

    public static function weatherForecast(): WeatherForecast
    {
        return WeatherForecast::fromOpenMeteo(self::forecastApiResponse());
    }

    /**
     * @return array<string, mixed>
     */
    public static function consultationAttributes(string $id = '11111111-1111-1111-1111-111111111111'): array
    {
        return [
            'id' => $id,
            'requested_city' => 'Chimoio',
            'requested_country' => 'Mozambique',
            'city' => 'Chimoio',
            'country' => 'Moçambique',
            'country_code' => 'MZ',
            'admin1' => 'Manica',
            'latitude' => -19.11639,
            'longitude' => 33.48333,
            'timezone' => 'Africa/Maputo',
            'weather_time' => '2026-09-08T12:45',
            'consulted_at' => now(),
            'current' => [
                'temperature' => 23.9,
                'temperatureUnit' => '°C',
                'humidity' => 64,
                'humidityUnit' => '%',
            ],
        ];
    }
}
