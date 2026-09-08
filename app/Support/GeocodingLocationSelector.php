<?php

namespace App\Support;

use App\DataTransferObjects\CityLocation;
use App\Exceptions\CityNotFoundException;
use Illuminate\Support\Collection;

class GeocodingLocationSelector
{
    /**
     * @param  Collection<int, CityLocation>  $locations
     */
    public static function select(Collection $locations, ?string $country): ?CityLocation
    {
        if ($locations->isEmpty()) {
            return null;
        }

        if ($country === null) {
            return $locations->first();
        }

        $matching = $locations
            ->filter(fn (CityLocation $location): bool => CountryMatcher::matches($location, $country))
            ->sortByDesc(fn (CityLocation $location): int => $location->population ?? 0)
            ->values();

        if ($matching->isNotEmpty()) {
            return $matching->first();
        }

        return $locations->first();
    }

    /**
     * @throws CityNotFoundException
     */
    public static function assertUsable(CityLocation $location, string $city, ?string $country): void
    {
        if ($country === null) {
            return;
        }

        if (! CountryMatcher::matches($location, $country)) {
            throw CityNotFoundException::countryMismatch($city, $country, $location->country);
        }

        $minimumPopulation = (int) config('services.open_meteo.geocoding_min_population', 10_000);

        if ($minimumPopulation <= 0) {
            return;
        }

        if ($location->population !== null && $location->population < $minimumPopulation) {
            throw CityNotFoundException::forCity($city, $country);
        }
    }

    public static function geocodingCount(?string $country): int
    {
        $configuredCount = (int) config('services.open_meteo.geocoding_count', 1);

        if ($country === null) {
            return max(1, $configuredCount);
        }

        return max($configuredCount, 5);
    }
}
