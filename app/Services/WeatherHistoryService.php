<?php

namespace App\Services;

use App\Exceptions\ConsultationNotFoundException;
use App\DataTransferObjects\WeatherHistoryFilters;
use App\DataTransferObjects\WeatherResult;
use App\Models\WeatherConsultation;
use App\Support\WeatherCurrentFormatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

// CRUD do histórico de consultas guardadas na base de dados.
class WeatherHistoryService
{
    // Grava o resultado de uma consulta na tabela weather_consultations.
    public function store(WeatherResult $result): WeatherConsultation
    {
        return WeatherConsultation::query()->create([
            'id' => $result->id,
            'requested_city' => $result->requestedCity,
            'requested_country' => $result->requestedCountry,
            'city' => $result->city->name,
            'country' => $result->city->country,
            'country_code' => $result->city->countryCode,
            'admin1' => $result->city->admin1,
            'latitude' => $result->forecast->latitude,
            'longitude' => $result->forecast->longitude,
            'timezone' => $result->forecast->timezone,
            'weather_time' => $result->weatherTime,
            'consulted_at' => $result->consultedAt,
            'current' => WeatherCurrentFormatter::format($result->forecast),
        ]);
    }

    /**
     * Lista todas as consultas filtradas (sem paginação).
     *
     * @return Collection<int, WeatherConsultation>
     */
    public function list(?WeatherHistoryFilters $filters = null): Collection
    {
        return $this->buildFilteredQuery($filters)
            ->orderByDesc('consulted_at')
            ->get();
    }

    /**
     * Lista consultas paginadas, opcionalmente filtradas por cidade, país ou datas.
     */
    public function paginate(
        ?WeatherHistoryFilters $filters = null,
        int $perPage = 15,
        int $page = 1,
    ): LengthAwarePaginator {
        return $this->buildFilteredQuery($filters)
            ->orderByDesc('consulted_at')
            ->paginate(perPage: $perPage, page: $page);
    }

    private function buildFilteredQuery(?WeatherHistoryFilters $filters): Builder
    {
        $query = WeatherConsultation::query();

        if ($filters !== null) {
            $this->applyFilters($query, $filters);
        }

        return $query;
    }

    /**
     * Busca uma consulta pelo ID ou lança 404.
     *
     * @throws ConsultationNotFoundException
     */
    public function findById(string $id): WeatherConsultation
    {
        $consultation = WeatherConsultation::query()->find($id);

        if ($consultation === null) {
            throw ConsultationNotFoundException::withId($id);
        }

        return $consultation;
    }

    /**
     * Remove uma consulta existente.
     *
     * @throws ConsultationNotFoundException
     */
    public function deleteById(string $id): void
    {
        $consultation = $this->findById($id);
        $consultation->delete();
    }

    // Aplica filtros de cidade, país e intervalo de datas à query.
    private function applyFilters(Builder $query, WeatherHistoryFilters $filters): void
    {
        if ($filters->city !== null) {
            $query->where(function (Builder $builder) use ($filters): void {
                $builder
                    ->where('city', 'like', '%'.$filters->city.'%')
                    ->orWhere('requested_city', 'like', '%'.$filters->city.'%');
            });
        }

        if ($filters->country !== null) {
            $query->where(function (Builder $builder) use ($filters): void {
                $builder
                    ->where('country', 'like', '%'.$filters->country.'%')
                    ->orWhere('requested_country', 'like', '%'.$filters->country.'%');
            });
        }

        if ($filters->startDate !== null) {
            $query->whereDate('consulted_at', '>=', $filters->startDate);
        }

        if ($filters->endDate !== null) {
            $query->whereDate('consulted_at', '<=', $filters->endDate);
        }
    }
}
