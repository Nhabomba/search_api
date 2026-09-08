<?php

namespace App\Services\Geocoding;

use App\Contracts\GeocodingServiceInterface;
use App\DataTransferObjects\CityLocation;
use App\Exceptions\GeocodingException;
use App\Exceptions\OpenMeteoHttpException;
use App\Services\OpenMeteo\OpenMeteoHttpClient;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;

// Adaptador de geocoding via API Open Meteo.
class OpenMeteoGeocodingService implements GeocodingServiceInterface
{
    public function __construct(
        private readonly OpenMeteoHttpClient $httpClient,
    ) {}

    /**
     * @return Collection<int, CityLocation>
     *
     * @throws GeocodingException
     * @throws ConnectionException
     */
    public function search(string $name, int $count, ?string $countryCode = null): Collection
    {
        $query = [
            'name' => $name,
            'count' => $count,
            'language' => config('services.open_meteo.language'),
            'format' => config('services.open_meteo.geocoding_format'),
        ];

        if ($countryCode !== null) {
            $query['countryCode'] = $countryCode;
        }

        try {
            $payload = $this->httpClient->get(
                service: 'geocoding',
                url: config('services.open_meteo.geocoding_url'),
                query: $query,
            );
        } catch (OpenMeteoHttpException $exception) {
            throw GeocodingException::fromHttpException($exception);
        }

        $results = $payload['results'] ?? [];

        if ($results === [] || ! is_array($results)) {
            return collect();
        }

        return collect($results)
            ->filter(fn (mixed $item): bool => is_array($item) && $this->hasRequiredFields($item))
            ->map(fn (array $item): CityLocation => CityLocation::fromOpenMeteo($item))
            ->values();
    }

    private function hasRequiredFields(array $item): bool
    {
        return isset(
            $item['id'],
            $item['name'],
            $item['latitude'],
            $item['longitude'],
            $item['country'],
            $item['country_code'],
            $item['timezone'],
        );
    }
}
