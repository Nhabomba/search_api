<?php

namespace App\Http\Resources;

use App\DataTransferObjects\WeatherResult;
use App\Models\WeatherConsultation;
use App\Support\WeatherCurrentFormatter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/** @mixin WeatherResult|WeatherConsultation */
// Formata consultas e histórico para o JSON público da API.
class WeatherResource extends JsonResource
{
    /**
     * Converte um resultado numa resposta HTTP JSON.
     */
    public static function toJsonResponse(WeatherResult|WeatherConsultation $data): JsonResponse
    {
        return (new self($data))->response();
    }

    /**
     * Converte uma lista de consultas numa resposta HTTP JSON.
     *
     * @param  Collection<int, WeatherConsultation>  $items
     */
    public static function toJsonCollectionResponse(Collection $items): JsonResponse
    {
        return self::collection($items)->response();
    }

    /**
     * Converte uma lista paginada de consultas numa resposta HTTP JSON.
     */
    public static function toJsonPaginatedResponse(LengthAwarePaginator $paginator): JsonResponse
    {
        return self::collection($paginator)->response();
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->resource instanceof WeatherConsultation) {
            return $this->formatConsultation($this->resource);
        }

        /** @var WeatherResult $result */
        $result = $this->resource;

        return [
            'id' => $result->id,
            'city' => $result->city->name,
            'country' => $result->city->country,
            'country_code' => $result->city->countryCode,
            'admin1' => $result->city->admin1,
            'latitude' => $result->forecast->latitude,
            'longitude' => $result->forecast->longitude,
            'timezone' => $result->forecast->timezone,
            'weatherTime' => $result->weatherTime,
            'consultedAt' => $result->consultedAt,
            'current' => WeatherCurrentFormatter::format($result->forecast),
        ];
    }

    // Formata um registo vindo da base de dados (histórico).
    private function formatConsultation(WeatherConsultation $consultation): array
    {
        return [
            'id' => $consultation->id,
            'city' => $consultation->city,
            'country' => $consultation->country,
            'country_code' => $consultation->country_code,
            'admin1' => $consultation->admin1,
            'latitude' => $consultation->latitude,
            'longitude' => $consultation->longitude,
            'timezone' => $consultation->timezone,
            'weatherTime' => $consultation->weather_time,
            'consultedAt' => $consultation->consulted_at->toIso8601String(),
            'current' => $consultation->current,
        ];
    }
}
