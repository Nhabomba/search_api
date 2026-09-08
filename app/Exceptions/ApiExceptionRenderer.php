<?php

namespace App\Exceptions;

use App\Http\Responses\ApiErrorResponse;
use App\Services\Logging\WeatherConsultationLogger;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

// Converte excepções da API em respostas JSON com o código HTTP correcto.
class ApiExceptionRenderer
{
    public function __construct(
        private readonly WeatherConsultationLogger $consultationLogger,
    ) {}

    // Mapeia excepções conhecidas para 404, 502, 503 ou 500 genérico.
    public function render(Throwable $exception, Request $request): ?\Illuminate\Http\JsonResponse
    {
        if (! $request->is('api/*')) {
            return null;
        }

        $requestId = $this->consultationLogger->ensureRequestId();

        if ($exception instanceof ValidationException) {
            return ApiErrorResponse::make(
                message: $exception->getMessage(),
                statusCode: 422,
                requestId: $requestId,
                errors: $exception->errors(),
            );
        }

        if ($exception instanceof CityNotFoundException || $exception instanceof ConsultationNotFoundException) {
            return ApiErrorResponse::make($exception->getMessage(), 404, $requestId);
        }

        if ($exception instanceof GeocodingException || $exception instanceof ForecastException) {
            return ApiErrorResponse::make($exception->getMessage(), 502, $requestId);
        }

        if ($exception instanceof ConnectionException) {
            $message = str_contains(strtolower($exception->getMessage()), 'timed out')
                ? 'A API da Open Meteo demorou demasiado a responder. Verifique a ligação à internet ou tente novamente.'
                : 'Não foi possível contactar a API da Open Meteo. Verifique a ligação à internet.';

            return ApiErrorResponse::make($message, 503, $requestId);
        }

        if ($exception instanceof HttpExceptionInterface) {
            return ApiErrorResponse::make(
                $exception->getMessage() ?: 'Pedido inválido.',
                $exception->getStatusCode(),
                $requestId,
            );
        }

        $this->logUnexpectedError($exception, $request, $requestId);

        return ApiErrorResponse::internalServerError($requestId);
    }

    private function logUnexpectedError(Throwable $exception, Request $request, string $requestId): void
    {
        Log::error('Erro inesperado na API', [
            'request_id' => $requestId,
            'method' => $request->method(),
            'path' => $request->path(),
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
        ]);
    }
}
