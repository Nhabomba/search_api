<?php

namespace Tests\Unit;

use App\Exceptions\OpenMeteoHttpException;
use App\Services\Logging\WeatherConsultationLogger;
use App\Services\OpenMeteo\OpenMeteoHttpClient;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class OpenMeteoHttpClientTest extends TestCase
{
    public function test_it_logs_successful_calls_with_duration_and_status(): void
    {
        $logger = Mockery::mock(WeatherConsultationLogger::class);
        $logger->shouldReceive('logExternalCall')
            ->once()
            ->with('geocoding', 200, Mockery::type('float'), 'success');

        Http::fake([
            'example.test/*' => Http::response(['results' => []]),
        ]);

        config([
            'services.open_meteo.timeout' => 10,
            'services.open_meteo.verify_ssl' => false,
        ]);

        $client = new OpenMeteoHttpClient($logger);

        $payload = $client->get('geocoding', 'https://example.test/search', ['name' => 'Beira']);

        $this->assertSame(['results' => []], $payload);
    }

    public function test_it_throws_when_response_is_invalid_json(): void
    {
        $logger = Mockery::mock(WeatherConsultationLogger::class);
        $logger->shouldReceive('logExternalCall')
            ->once()
            ->with('forecast', 200, Mockery::type('float'), 'invalid_json');

        Http::fake([
            'example.test/*' => Http::response('not-json'),
        ]);

        config(['services.open_meteo.verify_ssl' => false]);

        $client = new OpenMeteoHttpClient($logger);

        try {
            $client->get('forecast', 'https://example.test/forecast', []);
            $this->fail('Expected OpenMeteoHttpException was not thrown.');
        } catch (OpenMeteoHttpException $exception) {
            $this->assertSame('invalid_json', $exception->result);
        }
    }

    public function test_it_logs_client_errors(): void
    {
        $logger = Mockery::mock(WeatherConsultationLogger::class);
        $logger->shouldReceive('logExternalCall')
            ->once()
            ->with('forecast', 400, Mockery::type('float'), 'client_error');

        Http::fake([
            'example.test/*' => Http::response(['message' => 'Bad request'], 400),
        ]);

        config(['services.open_meteo.verify_ssl' => false]);

        $client = new OpenMeteoHttpClient($logger);

        try {
            $client->get('forecast', 'https://example.test/forecast', []);
            $this->fail('Expected OpenMeteoHttpException was not thrown.');
        } catch (OpenMeteoHttpException $exception) {
            $this->assertSame('client_error', $exception->result);
        }
    }
}
