<?php

namespace Tests\Unit;

use App\Support\WeatherCurrentFormatter;
use Tests\Support\FakeWeatherData;
use Tests\TestCase;

class WeatherCurrentFormatterTest extends TestCase
{
    public function test_it_maps_open_meteo_fields_to_api_format_with_units(): void
    {
        $formatted = WeatherCurrentFormatter::format(FakeWeatherData::weatherForecast());

        $this->assertSame([
            'temperature' => 23.9,
            'temperatureUnit' => '°C',
            'humidity' => 64,
            'humidityUnit' => '%',
            'apparentTemperature' => 23.0,
            'apparentTemperatureUnit' => '°C',
            'weatherCode' => 2,
            'weatherCodeUnit' => 'wmo code',
            'windSpeed' => 25.3,
            'windSpeedUnit' => 'km/h',
        ], $formatted);
    }
}
