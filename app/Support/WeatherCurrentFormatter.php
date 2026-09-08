<?php

namespace App\Support;

use App\DataTransferObjects\WeatherForecast;

// Converte campos da Open Meteo para o formato exposto na API.
class WeatherCurrentFormatter
{
    /** @var array<string, string> */
    private const FIELD_MAP = [
        'temperature_2m' => 'temperature',
        'relative_humidity_2m' => 'humidity',
        'apparent_temperature' => 'apparentTemperature',
        'weather_code' => 'weatherCode',
        'wind_speed_10m' => 'windSpeed',
    ];

    /**
     * Mapeia temperature_2m, humidity, etc. para nomes amigáveis com unidades.
     *
     * @return array<string, mixed>
     */
    public static function format(WeatherForecast $forecast): array
    {
        $formatted = [];

        foreach (self::FIELD_MAP as $sourceField => $targetField) {
            if (! isset($forecast->current[$sourceField])) {
                continue;
            }

            $formatted[$targetField] = $forecast->current[$sourceField];
            $formatted[$targetField.'Unit'] = $forecast->currentUnits[$sourceField] ?? null;
        }

        return $formatted;
    }
}
