<?php

namespace Tests\Unit;

use App\Services\WeatherCacheService;
use Illuminate\Support\Facades\Cache;
use Tests\Support\FakeWeatherData;
use Tests\TestCase;

class WeatherCacheServiceTest extends TestCase
{
    public function test_it_stores_and_retrieves_cached_weather_by_city_and_country(): void
    {
        config(['services.open_meteo.cache_ttl' => 300]);

        $service = new WeatherCacheService;
        $location = FakeWeatherData::cityLocation();
        $forecast = FakeWeatherData::weatherForecast();

        $service->put('Beira', 'Mozambique', $location, $forecast);

        $cached = $service->get('Beira', 'Mozambique');

        $this->assertNotNull($cached);
        $this->assertSame('Beira', $cached['location']['name']);
        $this->assertSame(23.9, $cached['forecast']['current']['temperature_2m']);
    }

    public function test_it_treats_city_with_and_without_country_as_different_cache_keys(): void
    {
        config(['services.open_meteo.cache_ttl' => 300]);

        $service = new WeatherCacheService;

        $service->put('Beira', 'Mozambique', FakeWeatherData::cityLocation(), FakeWeatherData::weatherForecast());

        $this->assertNotNull($service->get('Beira', 'Mozambique'));
        $this->assertNull($service->get('Beira', null));
    }

    public function test_it_does_not_cache_when_ttl_is_zero(): void
    {
        config(['services.open_meteo.cache_ttl' => 0]);

        Cache::spy();

        $service = new WeatherCacheService;

        $this->assertFalse($service->isEnabled());
        $this->assertNull($service->get('Beira', 'Mozambique'));

        $service->put('Beira', 'Mozambique', FakeWeatherData::cityLocation(), FakeWeatherData::weatherForecast());

        Cache::shouldNotHaveReceived('put');
    }
}
