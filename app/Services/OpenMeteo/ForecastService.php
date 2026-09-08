<?php

namespace App\Services\OpenMeteo;

use App\DataTransferObjects\WeatherForecast;
use App\Exceptions\ForecastException;
use App\Exceptions\OpenMeteoHttpException;
use Illuminate\Http\Client\ConnectionException;

// Chama a API de forecast da Open Meteo para obter o clima atual.
class ForecastService
{
    public function __construct(
        private readonly OpenMeteoHttpClient $httpClient,
    ) {}

    /**
     * Obtém o clima atual para as coordenadas indicadas.
     *
     * @throws ForecastException
     * @throws ConnectionException
     */
    public function getCurrentWeather(float $latitude, float $longitude): WeatherForecast
    {
        $currentFields = config('services.open_meteo.forecast_current_fields');

        try {
            $payload = $this->httpClient->get(
                service: 'forecast',
                url: config('services.open_meteo.forecast_url'),
                query: [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'current' => implode(',', $currentFields),
                    'timezone' => config('services.open_meteo.forecast_timezone'),
                ],
            );
        } catch (OpenMeteoHttpException $exception) {
            throw ForecastException::fromHttpException($exception);
        }

        if (
            ! isset($payload['latitude'], $payload['longitude'], $payload['timezone'])
            || ! is_array($payload['current'] ?? null)
            || ! is_array($payload['current_units'] ?? null)
        ) {
            throw ForecastException::invalidResponse();
        }

        return WeatherForecast::fromOpenMeteo($payload);
    }
}
