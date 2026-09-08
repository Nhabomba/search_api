<?php

namespace App\Services\Logging;

use App\DataTransferObjects\CityLocation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

// Regista cada etapa da consulta no ficheiro weather-consultations.log.
class WeatherConsultationLogger
{
    private ?string $requestId = null;

    private ?float $startedAt = null;

    // Inicia o ciclo de log e gera o request_id (UUID).
    public function start(string $city, ?string $country): string
    {
        $this->reset();

        $this->requestId = (string) Str::uuid();
        $this->startedAt = microtime(true);

        $this->write($this->formatLine('CONSULTA INICIO', [
            'request_id' => $this->requestId,
            'cidade' => $city,
            'pais' => $country ?? 'nao informado',
        ]));

        return $this->requestId;
    }

    // Garante um request_id para qualquer pedido da API (ex.: histórico, erros).
    public function ensureRequestId(): string
    {
        if ($this->requestId === null) {
            $this->requestId = (string) Str::uuid();
        }

        return $this->requestId;
    }

    public function requestId(): ?string
    {
        return $this->requestId;
    }

    // Regista resposta servida a partir da cache (sem chamar Open Meteo).
    public function logCacheHit(string $city, ?string $country): void
    {
        if ($this->requestId === null) {
            return;
        }

        $this->write($this->formatLine('CACHE HIT', [
            'request_id' => $this->requestId,
            'cidade' => $city,
            'pais' => $country ?? 'nao informado',
        ]));
    }

    // Regista chamada HTTP à Open Meteo (serviço, status, duração).
    public function logExternalCall(
        string $service,
        ?int $httpStatus,
        float $durationMs,
        string $result,
    ): void {
        if ($this->requestId === null) {
            return;
        }

        $this->write($this->formatLine('API EXTERNA', [
            'request_id' => $this->requestId,
            'servico' => $service,
            'http' => $httpStatus ?? 'sem resposta',
            'duracao_ms' => round($durationMs, 2),
            'resultado' => $result,
        ]));
    }

    // Regista a localização escolhida após o geocoding.
    public function logLocationSelected(CityLocation $location): void
    {
        if ($this->requestId === null) {
            return;
        }

        $this->write($this->formatLine('LOCALIZACAO SELECIONADA', [
            'request_id' => $this->requestId,
            'cidade' => $location->name,
            'pais' => $location->country,
            'country_code' => $location->countryCode,
            'admin1' => $location->admin1 ?? 'nao informado',
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
        ]));
    }

    // Regista quando nenhuma cidade válida foi encontrada.
    public function logLocationNotFound(): void
    {
        if ($this->requestId === null) {
            return;
        }

        $this->write($this->formatLine('LOCALIZACAO SELECIONADA', [
            'request_id' => $this->requestId,
            'resultado' => 'nenhuma localizacao valida encontrada',
        ]));
    }

    // Regista tentativa de guardar na base de dados.
    public function logPersistence(bool $success, ?string $recordId = null, ?string $message = null): void
    {
        if ($this->requestId === null) {
            return;
        }

        $details = [
            'request_id' => $this->requestId,
            'resultado' => $success ? 'sucesso' : 'falha',
        ];

        if ($recordId !== null) {
            $details['registo_id'] = $recordId;
        }

        if ($message !== null) {
            $details['mensagem'] = $message;
        }

        $this->write($this->formatLine('PERSISTENCIA', $details));
    }

    // Regista excepções capturadas durante a consulta.
    public function logException(Throwable $exception): void
    {
        if ($this->requestId === null) {
            return;
        }

        $this->write($this->formatLine('ERRO', [
            'request_id' => $this->requestId,
            'tipo' => $exception::class,
            'mensagem' => $exception->getMessage(),
        ]));
    }

    // Fecha o ciclo de log com resultado final e duração total.
    public function finish(string $result, ?string $message = null): void
    {
        if ($this->requestId === null) {
            return;
        }

        $details = [
            'request_id' => $this->requestId,
            'resultado' => $result,
        ];

        if ($this->startedAt !== null) {
            $details['duracao_total_ms'] = round((microtime(true) - $this->startedAt) * 1000, 2);
        }

        if ($message !== null) {
            $details['mensagem'] = $message;
        }

        $this->write($this->formatLine('CONSULTA FIM', $details));
    }

    /**
     * @param  array<string, mixed>  $details
     */
    private function formatLine(string $event, array $details): string
    {
        $parts = [$event];

        foreach ($details as $key => $value) {
            $parts[] = $key.'='.$value;
        }

        return implode(' | ', $parts);
    }

    private function write(string $line): void
    {
        Log::channel('weather_consultations')->info($line);
    }

    private function reset(): void
    {
        $this->requestId = null;
        $this->startedAt = null;
    }
}
