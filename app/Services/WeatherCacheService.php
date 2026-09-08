<?php

namespace App\Services;

use App\DataTransferObjects\CityLocation;
use App\DataTransferObjects\WeatherForecast;
use App\DataTransferObjects\WeatherResult;
use Illuminate\Support\Facades\Cache;

// Cache temporária dos dados da Open Meteo por cidade (+ país opcional).
class WeatherCacheService
{
    /**
     * @return array{location: array<string, mixed>, forecast: array<string, mixed>}|null
     */
    public function get(string $city, ?string $country): ?array
    {
        if (! $this->isEnabled()) {
            return null;
        }

        $cached = Cache::get($this->cacheKey($city, $country));

        return is_array($cached) ? $cached : null;
    }

    public function put(string $city, ?string $country, CityLocation $location, WeatherForecast $forecast): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        Cache::put(
            $this->cacheKey($city, $country),
            $this->serialize($location, $forecast),
            $this->ttl(),
        );
    }

    /**
     * @param  array{location: array<string, mixed>, forecast: array<string, mixed>}  $cached
     */
    public function toWeatherResult(
        array $cached,
        string $requestId,
        string $requestedCity,
        ?string $requestedCountry,
    ): WeatherResult {
        $location = $this->deserializeLocation($cached['location']);
        $forecast = $this->deserializeForecast($cached['forecast']);

        return new WeatherResult(
            id: $requestId,
            requestedCity: $requestedCity,
            requestedCountry: $requestedCountry,
            city: $location,
            forecast: $forecast,
            weatherTime: (string) ($forecast->current['time'] ?? ''),
            consultedAt: now()->toIso8601String(),
        );
    }

    public function isEnabled(): bool
    {
        return $this->ttl() > 0;
    }

    private function ttl(): int
    {
        return (int) config('services.open_meteo.cache_ttl', 300);
    }

    private function cacheKey(string $city, ?string $country): string
    {
        $normalized = mb_strtolower(trim($city)).'|'.mb_strtolower(trim($country ?? ''));

        return 'weather:'.hash('xxh128', $normalized);
    }

    /**
     * @return array{location: array<string, mixed>, forecast: array<string, mixed>}
     */
    private function serialize(CityLocation $location, WeatherForecast $forecast): array
    {
        return [
            'location' => [
                'name' => $location->name,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'country' => $location->country,
                'countryCode' => $location->countryCode,
                'admin1' => $location->admin1,
                'population' => $location->population,
            ],
            'forecast' => [
                'latitude' => $forecast->latitude,
                'longitude' => $forecast->longitude,
                'timezone' => $forecast->timezone,
                'current' => $forecast->current,
                'currentUnits' => $forecast->currentUnits,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function deserializeLocation(array $data): CityLocation
    {
        return new CityLocation(
            name: (string) $data['name'],
            latitude: (float) $data['latitude'],
            longitude: (float) $data['longitude'],
            country: (string) $data['country'],
            countryCode: (string) $data['countryCode'],
            admin1: isset($data['admin1']) ? (string) $data['admin1'] : null,
            population: isset($data['population']) ? (int) $data['population'] : null,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function deserializeForecast(array $data): WeatherForecast
    {
        return new WeatherForecast(
            latitude: (float) $data['latitude'],
            longitude: (float) $data['longitude'],
            timezone: (string) $data['timezone'],
            current: (array) $data['current'],
            currentUnits: (array) $data['currentUnits'],
        );
    }
}
