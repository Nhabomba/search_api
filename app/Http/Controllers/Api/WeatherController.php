<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WeatherHistoryRequest;
use App\Http\Requests\WeatherRequest;
use App\Http\Resources\WeatherResource;
use App\DataTransferObjects\WeatherHistoryFilters;
use App\Services\OpenMeteo\WeatherService;
use App\Services\WeatherHistoryService;
use Illuminate\Http\JsonResponse;

// Endpoints públicos de consulta e histórico de clima.
class WeatherController extends Controller
{
    public function __construct(
        private readonly WeatherService $weatherService,
        private readonly WeatherHistoryService $weatherHistoryService,
    ) {}

    // GET /api/weather  consulta clima por cidade.
    public function current(WeatherRequest $request): JsonResponse
    {
        $weather = $this->weatherService->getByCity(
            city: $request->city(),
            country: $request->country(),
        );

        return WeatherResource::toJsonResponse($weather);
    }

    // GET /api/weather/history  lista consultas com filtros opcionais.
    public function history(WeatherHistoryRequest $request): JsonResponse
    {
        $filters = new WeatherHistoryFilters(
            city: $request->city(),
            country: $request->country(),
            startDate: $request->startDate(),
            endDate: $request->endDate(),
        );

        if (config('services.weather_history.pagination_enabled')) {
            return WeatherResource::toJsonPaginatedResponse(
                $this->weatherHistoryService->paginate(
                    filters: $filters,
                    perPage: $request->perPage(),
                    page: $request->page(),
                ),
            );
        }

        return WeatherResource::toJsonCollectionResponse(
            $this->weatherHistoryService->list($filters),
        );
    }

    // GET /api/weather/history/{id}  detalhe de uma consulta.
    public function show(string $id): JsonResponse
    {
        return WeatherResource::toJsonResponse(
            $this->weatherHistoryService->findById($id),
        );
    }

    // DELETE /api/weather/history/{id}  remove uma consulta.
    public function destroy(string $id): JsonResponse
    {
        $this->weatherHistoryService->deleteById($id);

        return response()->json([
            'message' => 'Consulta eliminada com sucesso.',
        ]);
    }
}
