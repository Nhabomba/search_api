<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

// Verifica se a aplicação e a base de dados estão operacionais.
class HealthController extends Controller
{
    // GET /health — devolve status da app e ligação à BD.
    public function __invoke(): JsonResponse
    {
        $checks = [
            'application' => 'ok',
            'database' => $this->checkDatabase(),
        ];

        $status = in_array('error', $checks, true) ? 'error' : 'ok';
        $httpStatus = $status === 'ok' ? 200 : 503;

        return response()->json([
            'status' => $status,
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ], $httpStatus);
    }

    // Testa a ligação PDO com a base de dados.
    private function checkDatabase(): string
    {
        try {
            DB::connection()->getPdo();

            return 'ok';
        } catch (Throwable) {
            return 'error';
        }
    }
}
