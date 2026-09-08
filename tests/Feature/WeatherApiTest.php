<?php

namespace Tests\Feature;

use App\Models\WeatherConsultation;
use App\Services\WeatherHistoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Support\FakeWeatherData;
use Tests\TestCase;

class WeatherApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_weather_endpoint_returns_mapped_response_using_fake_integrations(): void
    {
        Http::fake([
            'geocoding-api.open-meteo.com/*' => Http::response([
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
                    ],
                ],
            ]),
            'api.open-meteo.com/*' => Http::response([
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
            ]),
        ]);

        $response = $this->getJson('/api/weather?city=Beira&country=Mozambique');

        $response
            ->assertOk()
            ->assertJsonPath('data.city', 'Beira')
            ->assertJsonPath('data.current.temperature', 23.9)
            ->assertJsonPath('data.current.humidityUnit', '%')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'city',
                    'country',
                    'weatherTime',
                    'consultedAt',
                    'current' => [
                        'temperature',
                        'temperatureUnit',
                        'humidity',
                        'humidityUnit',
                    ],
                ],
            ]);

        $this->assertDatabaseCount('weather_consultations', 1);
    }

    public function test_weather_endpoint_uses_cache_for_repeated_city_requests(): void
    {
        config(['services.open_meteo.cache_ttl' => 300]);

        WeatherConsultation::query()->delete();
        Http::fake([
            'geocoding-api.open-meteo.com/*' => Http::response([
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
                    ],
                ],
            ]),
            'api.open-meteo.com/*' => Http::response([
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
            ]),
        ]);

        $this->getJson('/api/weather?city=Beira&country=Mozambique')->assertOk();
        $this->getJson('/api/weather?city=Beira&country=Mozambique')->assertOk();

        Http::assertSentCount(2);
        $this->assertDatabaseCount('weather_consultations', 2);
    }

    public function test_weather_endpoint_returns_404_when_city_is_not_in_requested_country(): void
    {
        config(['services.open_meteo.geocoding_min_population' => 10_000]);

        Http::fake([
            'geocoding-api.open-meteo.com/*' => Http::response(FakeWeatherData::pretoriaMozambiqueGeocodingApiResponse()),
        ]);

        $this->getJson('/api/weather?city=Pretoria&country=Mozambique')
            ->assertNotFound()
            ->assertJsonPath('message', "Cidade 'Pretoria' não encontrada em 'Mozambique'.")
            ->assertJsonStructure(['message', 'requestId']);
    }

    public function test_weather_endpoint_returns_404_when_geocoding_country_does_not_match(): void
    {
        Http::fake([
            'geocoding-api.open-meteo.com/*' => Http::response(FakeWeatherData::pretoriaGeocodingApiResponse()),
        ]);

        $this->getJson('/api/weather?city=Pretoria&country=Mozambique')
            ->assertNotFound()
            ->assertJsonPath(
                'message',
                "Cidade 'Pretoria' não encontrada em 'Mozambique'. A localização encontrada pertence a 'South Africa'."
            );
    }

    public function test_weather_endpoint_returns_weather_for_matching_city_and_country(): void
    {
        Http::fake([
            'geocoding-api.open-meteo.com/*' => Http::response(FakeWeatherData::pretoriaGeocodingApiResponse()),
            'api.open-meteo.com/*' => Http::response(FakeWeatherData::forecastApiResponse()),
        ]);

        $this->getJson('/api/weather?city=Pretoria&country=South Africa')
            ->assertOk()
            ->assertJsonPath('data.city', 'Pretoria')
            ->assertJsonPath('data.country', 'South Africa');
    }

    public function test_weather_endpoint_returns_404_when_city_is_not_found(): void
    {
        Http::fake([
            'geocoding-api.open-meteo.com/*' => Http::response(['results' => []]),
        ]);

        $this->getJson('/api/weather?city=CidadeInexistente&country=Mozambique')
            ->assertNotFound()
            ->assertJsonStructure(['message', 'requestId']);
    }

    public function test_weather_endpoint_returns_502_when_external_api_fails(): void
    {
        Http::fake([
            'geocoding-api.open-meteo.com/*' => Http::response(['error' => true, 'reason' => 'Invalid request'], 400),
        ]);

        $this->getJson('/api/weather?city=Beira&country=Mozambique')
            ->assertStatus(502);
    }

    public function test_history_endpoint_returns_consultations(): void
    {
        config(['services.weather_history.pagination_enabled' => true]);

        WeatherConsultation::query()->create([
            'id' => '11111111-1111-1111-1111-111111111111',
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
            ],
        ]);

        $this->getJson('/api/weather/history?city=Chimoio')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data',
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ]);
    }

    public function test_history_endpoint_supports_pagination(): void
    {
        config(['services.weather_history.pagination_enabled' => true]);

        WeatherConsultation::query()->delete();

        for ($index = 1; $index <= 3; $index++) {
            WeatherConsultation::query()->create([
                'id' => sprintf('11111111-1111-1111-1111-1111111111%02d', $index),
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
                'consulted_at' => now()->subMinutes($index),
                'current' => [
                    'temperature' => 20 + $index,
                    'temperatureUnit' => '°C',
                ],
            ]);
        }

        $this->getJson('/api/weather/history?city=Chimoio&perPage=2&page=1')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 2);

        $this->getJson('/api/weather/history?city=Chimoio&perPage=2&page=2')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.current_page', 2);
    }

    public function test_history_endpoint_returns_all_results_when_pagination_is_disabled(): void
    {
        config(['services.weather_history.pagination_enabled' => false]);

        WeatherConsultation::query()->delete();

        for ($index = 1; $index <= 3; $index++) {
            WeatherConsultation::query()->create([
                'id' => sprintf('22222222-2222-2222-2222-2222222222%02d', $index),
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
                'consulted_at' => now()->subMinutes($index),
                'current' => [
                    'temperature' => 20 + $index,
                    'temperatureUnit' => '°C',
                ],
            ]);
        }

        $this->getJson('/api/weather/history?city=Chimoio&perPage=2&page=1')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonMissingPath('meta')
            ->assertJsonMissingPath('links');
    }

    public function test_unexpected_errors_return_generic_500_without_sensitive_details(): void
    {
        config(['services.weather_history.pagination_enabled' => true]);

        $this->mock(WeatherHistoryService::class, function ($mock): void {
            $mock->shouldReceive('paginate')
                ->once()
                ->andThrow(new \RuntimeException('SQLSTATE connection string C:\\wamp64\\secrets'));
        });

        $response = $this->getJson('/api/weather/history');

        $response
            ->assertStatus(500)
            ->assertJson([
                'message' => 'Ocorreu um erro interno no servidor.',
            ])
            ->assertJsonStructure(['message', 'requestId']);
    }

    public function test_validation_errors_include_request_id(): void
    {
        $this->getJson('/api/weather')
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors', 'requestId']);
    }

    public function test_show_returns_consultation_by_id(): void
    {
        $id = '33333333-3333-3333-3333-333333333333';

        WeatherConsultation::query()->create(
            FakeWeatherData::consultationAttributes($id),
        );

        $this->getJson("/api/weather/history/{$id}")
            ->assertOk()
            ->assertJsonPath('data.id', $id)
            ->assertJsonPath('data.city', 'Chimoio');
    }

    public function test_show_returns_404_when_consultation_not_found(): void
    {
        $this->getJson('/api/weather/history/00000000-0000-0000-0000-000000000000')
            ->assertNotFound()
            ->assertJsonStructure(['message', 'requestId']);
    }

    public function test_destroy_deletes_consultation(): void
    {
        $id = '44444444-4444-4444-4444-444444444444';

        WeatherConsultation::query()->create(
            FakeWeatherData::consultationAttributes($id),
        );

        $this->deleteJson("/api/weather/history/{$id}")
            ->assertOk()
            ->assertJsonPath('message', 'Consulta eliminada com sucesso.');

        $this->assertDatabaseMissing('weather_consultations', ['id' => $id]);
    }

    public function test_health_endpoint_returns_ok_when_database_is_available(): void
    {
        $this->getJson('/health')
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('checks.database', 'ok');
    }
}
