<?php

namespace App\Http\Middleware;

use App\Services\Logging\WeatherConsultationLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Adiciona o X-Request-ID ao response quando uma consulta foi feita.
class AttachRequestId
{
    public function __construct(
        private readonly WeatherConsultationLogger $consultationLogger,
    ) {}

    /**
     * Propaga o request_id gerado pelo logger no header da resposta.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->consultationLogger->ensureRequestId();

        $response = $next($request);

        $requestId = $this->consultationLogger->requestId();

        if ($requestId !== null) {
            $response->headers->set('X-Request-ID', $requestId);
        }

        return $response;
    }
}
