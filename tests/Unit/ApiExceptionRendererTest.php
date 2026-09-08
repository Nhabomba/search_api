<?php

namespace Tests\Unit;

use App\Exceptions\ApiExceptionRenderer;
use App\Exceptions\CityNotFoundException;
use App\Services\Logging\WeatherConsultationLogger;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class ApiExceptionRendererTest extends TestCase
{
    public function test_it_returns_404_with_request_id_for_city_not_found(): void
    {
        $logger = Mockery::mock(WeatherConsultationLogger::class);
        $logger->shouldReceive('ensureRequestId')->once()->andReturn('test-request-id');

        $renderer = new ApiExceptionRenderer($logger);
        $request = Request::create('/api/weather', 'GET');

        $response = $renderer->render(
            CityNotFoundException::forCity('Beira', 'Mozambique'),
            $request,
        );

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([
            'message' => "Cidade 'Beira' não encontrada em 'Mozambique'.",
            'requestId' => 'test-request-id',
        ], $response->getData(true));
    }

    public function test_it_returns_503_with_request_id_for_connection_timeout(): void
    {
        $logger = Mockery::mock(WeatherConsultationLogger::class);
        $logger->shouldReceive('ensureRequestId')->once()->andReturn('timeout-request-id');

        $renderer = new ApiExceptionRenderer($logger);
        $request = Request::create('/api/weather', 'GET');

        $response = $renderer->render(
            new ConnectionException('cURL error 28: Connection timed out'),
            $request,
        );

        $this->assertSame(503, $response->getStatusCode());
        $this->assertSame('timeout-request-id', $response->getData(true)['requestId']);
    }
}
