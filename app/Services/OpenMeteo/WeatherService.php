<?php

namespace App\Services\OpenMeteo;

use App\Contracts\GeocodingServiceInterface;
use App\DataTransferObjects\WeatherResult;
use App\Exceptions\CityNotFoundException;
use App\Exceptions\ForecastException;
use App\Exceptions\GeocodingException;
use App\Support\CountryCodeResolver;
use App\Support\GeocodingLocationSelector;
use App\Services\Logging\WeatherConsultationLogger;
use App\Services\WeatherCacheService;
use App\Services\WeatherHistoryService;
use Illuminate\Http\Client\ConnectionException;
use Throwable;

// Orquestra o fluxo completo: geocoding, clima, persistência e logs.
class WeatherService
{
    public function __construct(
        private readonly GeocodingServiceInterface $geocodingService,
        private readonly ForecastService $forecastService,
        private readonly WeatherHistoryService $weatherHistoryService,
        private readonly WeatherConsultationLogger $consultationLogger,
        private readonly WeatherCacheService $weatherCache,
    ) {}

    /**
     * Consulta o clima de uma cidade (Open Meteo geocoding + forecast).
     *
     * @throws CityNotFoundException
     * @throws ForecastException
     * @throws GeocodingException
     * @throws ConnectionException
     */
    public function getByCity(string $city, ?string $country = null): WeatherResult
    {
        $requestId = $this->consultationLogger->start($city, $country);

        try {
            $cached = $this->weatherCache->get($city, $country);

            if ($cached !== null) {
                $cachedLocation = $this->weatherCache->toWeatherResult(
                    cached: $cached,
                    requestId: 'validation-only',
                    requestedCity: $city,
                    requestedCountry: $country,
                )->city;

                try {
                    GeocodingLocationSelector::assertUsable($cachedLocation, $city, $country);
                } catch (CityNotFoundException) {
                    $cached = null;
                }
            }

            if ($cached !== null) {
                $this->consultationLogger->logCacheHit($city, $country);

                $result = $this->weatherCache->toWeatherResult(
                    cached: $cached,
                    requestId: $requestId,
                    requestedCity: $city,
                    requestedCountry: $country,
                );

                $this->persistConsultation($result);
                $this->consultationLogger->finish('success');

                return $result;
            }

            // Monta a pesquisa: usa countryCode (ISO) quando possível, senão "cidade, país".
            $countryCode = CountryCodeResolver::resolve($country);
            $geocodingName = $countryCode !== null ? $city : ($country !== null ? $city.', '.$country : $city);

            // 1. Obter coordenadas da cidade.
            $locations = $this->geocodingService->search(
                name: $geocodingName,
                count: GeocodingLocationSelector::geocodingCount($country),
                countryCode: $countryCode,
            );

            $location = GeocodingLocationSelector::select($locations, $country);

            if ($location === null) {
                $this->consultationLogger->logLocationNotFound();

                throw CityNotFoundException::forCity($city, $country);
            }

            GeocodingLocationSelector::assertUsable($location, $city, $country);

            $this->consultationLogger->logLocationSelected($location);

            // 2. Obter clima atual com latitude/longitude.
            $forecast = $this->forecastService->getCurrentWeather(
                latitude: $location->latitude,
                longitude: $location->longitude,
            );

            $result = new WeatherResult(
                id: $requestId,
                requestedCity: $city,
                requestedCountry: $country,
                city: $location,
                forecast: $forecast,
                weatherTime: (string) ($forecast->current['time'] ?? ''),
                consultedAt: now()->toIso8601String(),
            );

            // 4. Guardar em cache para consultas futuras da mesma cidade.
            $this->weatherCache->put($city, $country, $location, $forecast);

            // 3. Guardar na BD (erro aqui não bloqueia a resposta).
            $this->persistConsultation($result);

            $this->consultationLogger->finish('success');

            return $result;
        } catch (Throwable $exception) {
            $this->consultationLogger->logException($exception);
            $this->consultationLogger->finish('error', $exception->getMessage());

            throw $exception;
        }
    }

    // Persiste a consulta e regista sucesso ou falha no log.
    private function persistConsultation(WeatherResult $result): void
    {
        try {
            $this->weatherHistoryService->store($result);

            $this->consultationLogger->logPersistence(
                success: true,
                recordId: $result->id,
            );
        } catch (Throwable $exception) {
            $this->consultationLogger->logPersistence(
                success: false,
                recordId: $result->id,
                message: $exception->getMessage(),
            );

            $this->consultationLogger->logException($exception);
        }
    }
}
