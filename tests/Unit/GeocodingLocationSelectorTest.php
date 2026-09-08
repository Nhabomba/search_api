<?php

namespace Tests\Unit;

use App\Exceptions\CityNotFoundException;
use App\Support\GeocodingLocationSelector;
use Illuminate\Support\Collection;
use Tests\Support\FakeWeatherData;
use Tests\TestCase;

class GeocodingLocationSelectorTest extends TestCase
{
    public function test_it_rejects_small_population_matches_when_country_is_provided(): void
    {
        config(['services.open_meteo.geocoding_min_population' => 10_000]);

        $location = new \App\DataTransferObjects\CityLocation(
            name: 'Pretoria',
            latitude: -25.36722,
            longitude: 32.95944,
            country: 'Moçambique',
            countryCode: 'MZ',
            admin1: 'Província de Maputo',
            population: 120,
        );

        $this->expectException(CityNotFoundException::class);

        GeocodingLocationSelector::assertUsable($location, 'Pretoria', 'Mozambique');
    }

    public function test_it_selects_the_most_populated_match_for_a_country(): void
    {
        $small = new \App\DataTransferObjects\CityLocation(
            name: 'Pretoria',
            latitude: -25.36722,
            longitude: 32.95944,
            country: 'Moçambique',
            countryCode: 'MZ',
            admin1: 'Província de Maputo',
            population: 120,
        );

        $large = FakeWeatherData::cityLocation();

        $selected = GeocodingLocationSelector::select(
            Collection::make([$small, $large]),
            'Mozambique',
        );

        $this->assertSame('Beira', $selected?->name);
    }

    public function test_it_requests_more_results_when_country_is_provided(): void
    {
        config(['services.open_meteo.geocoding_count' => 1]);

        $this->assertSame(5, GeocodingLocationSelector::geocodingCount('Mozambique'));
        $this->assertSame(1, GeocodingLocationSelector::geocodingCount(null));
    }
}
