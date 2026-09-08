<?php

namespace App\Services\OpenMeteo;

use App\Exceptions\OpenMeteoHttpException;
use App\Services\Logging\WeatherConsultationLogger;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

// Cliente HTTP partilhado para as APIs da Open Meteo (timeout, logs, erros).
class OpenMeteoHttpClient
{
    public function __construct(
        private readonly WeatherConsultationLogger $consultationLogger,
    ) {}

    /**
     * Executa um GET e valida a resposta JSON.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     *
     * @throws OpenMeteoHttpException
     * @throws ConnectionException
     */
    public function get(string $service, string $url, array $query): array
    {
        $startedAt = microtime(true);

        try {
            $options = [
                'verify' => config('services.open_meteo.verify_ssl'),
            ];

            if (config('services.open_meteo.force_ipv4')) {
                $options['curl'] = [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                ];
            }

            $response = Http::timeout((int) config('services.open_meteo.timeout', 30))
                ->connectTimeout((int) config('services.open_meteo.connect_timeout', 10))
                ->acceptJson()
                ->withOptions($options)
                ->get($url, $query);

            $durationMs = $this->elapsedMilliseconds($startedAt);

            if ($response->successful()) {
                try {
                    $payload = $this->parseSuccessfulResponse($response);

                    $this->logCall(
                        service: $service,
                        status: $response->status(),
                        durationMs: $durationMs,
                        result: 'success',
                    );

                    return $payload;
                } catch (OpenMeteoHttpException $exception) {
                    $this->logCall(
                        service: $service,
                        status: $response->status(),
                        durationMs: $durationMs,
                        result: $exception->result,
                    );

                    throw $exception;
                }
            }

            $result = $response->serverError() ? 'server_error' : 'client_error';

            $this->logCall(
                service: $service,
                status: $response->status(),
                durationMs: $durationMs,
                result: $result,
            );

            throw OpenMeteoHttpException::httpError(
                service: $service,
                statusCode: $response->status(),
                result: $result,
            );
        } catch (ConnectionException $exception) {
            $this->logCall(
                service: $service,
                status: null,
                durationMs: $this->elapsedMilliseconds($startedAt),
                result: 'connection_error',
            );

            throw $exception;
        } catch (OpenMeteoHttpException $exception) {
            throw $exception;
        }
    }

    // Valida corpo, JSON e erros devolvidos pela Open Meteo.
    private function parseSuccessfulResponse(Response $response): array
    {
        if ($response->body() === '') {
            throw OpenMeteoHttpException::emptyResponse($response->status());
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            throw OpenMeteoHttpException::invalidJson($response->status());
        }

        if (($payload['error'] ?? false) === true) {
            throw OpenMeteoHttpException::apiError(
                reason: (string) ($payload['reason'] ?? 'Erro desconhecido da Open Meteo.'),
                statusCode: $response->status(),
            );
        }

        return $payload;
    }

    private function logCall(
        string $service,
        ?int $status,
        float $durationMs,
        string $result,
    ): void {
        $this->consultationLogger->logExternalCall(
            service: $service,
            httpStatus: $status,
            durationMs: $durationMs,
            result: $result,
        );
    }

    private function elapsedMilliseconds(float $startedAt): float
    {
        return (microtime(true) - $startedAt) * 1000;
    }
}
