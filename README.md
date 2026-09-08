# search_api

API REST em Laravel 12 para consulta de clima por cidade, com histórico em MySQL, cache e integração com [Open Meteo](https://open-meteo.com/) (geocoding + forecast).

## Requisitos

- PHP 8.2+
- Composer
- MySQL (ex.: WAMP)
- Extensões PHP: `pdo_mysql`, `mbstring`, `openssl`, `curl`

## Instalação

```bash
git clone <repo-url> search_api
cd search_api
composer install
cp .env.example .env
php artisan key:generate
```

Criar a base de dados MySQL `search_api` e configurar no `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=search_api
DB_USERNAME=root
DB_PASSWORD=
```

**Opção A — migrations (instalação limpa):**

```bash
php artisan migrate
php artisan serve
```

**Opção B — importar BD de teste (recomendado para demonstração):**

Na pasta `database/BaseDeDados-Teste/` existe um dump MySQL com estrutura e dados de exemplo:

```
database/BaseDeDados-Teste/search_api.sql
```

Importar via phpMyAdmin (WAMP) ou linha de comandos:

```bash
mysql -u root -p search_api < database/BaseDeDados-Teste/search_api.sql
```

No Windows (PowerShell), se o MySQL do WAMP estiver no PATH:

```powershell
Get-Content database\BaseDeDados-Teste\search_api.sql | mysql -u root search_api
```

O dump inclui:

- Tabelas da aplicação (`weather_consultations`, `cache`, migrations, etc.)
- **6 consultas de histórico** (Maputo, Inhambane, Tete, Pemba, Nampula, São Paulo)
- Entradas de **cache** de clima para testar respostas rápidas

Útil para testar `/api/weather/history` e `/api/weather/history/{id}` sem fazer consultas reais à Open Meteo.

Depois de importar:

```bash
php artisan serve
```

API disponível em `http://127.0.0.1:8000`.

---

## Endpoints

| Método | URL | Descrição |
|--------|-----|-----------|
| `GET` | `/api/weather` | Consulta clima por cidade |
| `GET` | `/api/weather/history` | Lista histórico de consultas |
| `GET` | `/api/weather/history/{id}` | Detalhe de uma consulta |
| `DELETE` | `/api/weather/history/{id}` | Elimina uma consulta |
| `GET` | `/health` | Health check (app + base de dados) |

Todos os pedidos `/api/*` devolvem JSON e incluem `X-Request-ID` no header.

---

## Testar manualmente (API)

### 1. Health check

```bash
curl http://127.0.0.1:8000/health
```

Resposta esperada (200):

```json
{
  "status": "ok",
  "checks": { "application": "ok", "database": "ok" },
  "timestamp": "..."
}
```

### 2. Consultar clima

```bash
curl "http://127.0.0.1:8000/api/weather?city=Beira&country=Mozambique"
```

Parâmetros:

| Parâmetro | Obrigatório | Descrição |
|-----------|-------------|-----------|
| `city` | Sim | Nome da cidade |
| `country` | Não | País (recomendado para evitar homónimos) |

Resposta de sucesso (200) — estrutura principal:

```json
{
  "data": {
    "id": "uuid",
    "city": "Beira",
    "country": "Moçambique",
    "country_code": "MZ",
    "current": {
      "temperature": 23.9,
      "temperatureUnit": "°C",
      "humidity": 64,
      "humidityUnit": "%"
    },
    "weatherTime": "...",
    "consultedAt": "..."
  }
}
```

### 3. Histórico

Com a BD de teste importada (`database/BaseDeDados-Teste/search_api.sql`), podes listar consultas já existentes:

```bash
curl "http://127.0.0.1:8000/api/weather/history"
curl "http://127.0.0.1:8000/api/weather/history?city=Maputo&country=Mozambique"
curl "http://127.0.0.1:8000/api/weather/history/e0a5345c-d3cc-4a87-b881-b8e90cbe44de"
```

Outros exemplos:

```bash
curl "http://127.0.0.1:8000/api/weather/history?city=Beira&page=1&perPage=10"
curl "http://127.0.0.1:8000/api/weather/history/{id}"
curl -X DELETE "http://127.0.0.1:8000/api/weather/history/{id}"
```

Filtros opcionais: `city`, `country`, `startDate`, `endDate` (formato `YYYY-MM-DD`), `page`, `perPage` (máx. 100).

### 4. Cenários úteis para validar

| Teste | URL | Resultado esperado |
|-------|-----|-------------------|
| Cidade inexistente | `?city=CidadeInexistente&country=Mozambique` | 404 |
| País errado | `?city=Maputo&country=England` | 404 |
| Sem `city` | `/api/weather` | 422 |
| Open Meteo offline | qualquer consulta nova | 503 ou 502 |

---

## Erros da API

Formato padrão:

```json
{
  "message": "Descrição do erro",
  "requestId": "uuid"
}
```

| HTTP | Quando |
|------|--------|
| **422** | Validação (ex.: `city` em falta) |
| **404** | Cidade/país não encontrado ou consulta inexistente |
| **502** | Falha na Open Meteo (4xx/5xx upstream) |
| **503** | Timeout ou sem ligação à Open Meteo |
| **500** | Erro interno inesperado |

---

## Testes automatizados

A suite usa **PHPUnit** via Laravel. Os testes **não chamam a Open Meteo de verdade** — usam `Http::fake()` para simular respostas.

### Pré-requisitos

- MySQL a correr com a BD `search_api` (configurada no `phpunit.xml`)
- Dependências instaladas: `composer install`

> **Nota:** Os testes automatizados usam `RefreshDatabase` e recriam as tabelas a cada execução — **não usam** o dump `database/BaseDeDados-Teste/search_api.sql`. Esse ficheiro destina-se a **testes manuais** da API (histórico, cache, demonstração).

### Executar todos os testes

```powershell
cd c:\wamp64\www\search_api
php artisan test
```

### Apenas testes unitários

```powershell
php artisan test --testsuite=Unit
```

### Apenas testes de feature (API)

```powershell
php artisan test --testsuite=Feature
```

### Um ficheiro ou teste específico

```powershell
php artisan test tests/Unit/CountryMatcherTest.php
php artisan test --filter=GeocodingLocationSelectorTest
php artisan test --filter=test_weather_endpoint_returns_mapped_response_using_fake_integrations
```

### Suites disponíveis

| Pasta | Ficheiros | O que testam |
|-------|-----------|--------------|
| `tests/Unit/` | 7 classes | Lógica isolada (matcher, cache, formatter, HTTP client, erros) |
| `tests/Feature/` | `WeatherApiTest.php` | Endpoints HTTP completos com mocks |

**Total actual:** 33 testes.

### O que é mockado nos testes

- **`Http::fake()`** — simula geocoding e forecast da Open Meteo
- **`Mockery`** — simula o logger em testes unitários do HTTP client e do renderer de erros
- **`FakeWeatherData`** — dados fixos reutilizáveis (não é mock; são DTOs de teste)

---

## Configuração (.env)

### Open Meteo

| Variável | Default | Descrição |
|----------|---------|-----------|
| `OPEN_METEO_GEOCODING_URL` | URL Open Meteo | Endpoint de geocoding |
| `OPEN_METEO_FORECAST_URL` | URL Open Meteo | Endpoint de forecast |
| `OPEN_METEO_LANGUAGE` | `pt` | Idioma do geocoding |
| `OPEN_METEO_GEOCODING_MIN_POPULATION` | `10000` | Rejeita homónimos pequenos |
| `OPEN_METEO_TIMEOUT` | `30` | Timeout HTTP (segundos) |
| `OPEN_METEO_CACHE_TTL` | `300` | Cache por cidade (segundos; `0` desactiva) |

### Histórico

| Variável | Default | Descrição |
|----------|---------|-----------|
| `WEATHER_HISTORY_PAGINATION_ENABLED` | `true` | Paginação no histórico |
| `WEATHER_HISTORY_PER_PAGE` | `15` | Itens por página |

---

## Arquitectura (resumo)

```
Routes → Middleware → Controller → FormRequest → WeatherService
                                                      ↓
                              GeocodingServiceInterface → OpenMeteoGeocodingService
                              ForecastService → OpenMeteoHttpClient
                              WeatherCacheService / WeatherHistoryService
```

---

## Comandos úteis

```bash
php artisan migrate       # Criar/atualizar tabelas
php artisan cache:clear   # Limpar cache (útil após mudar validação/cache)
php artisan test          # Correr testes
php artisan serve         # Servidor de desenvolvimento
```

---

## Licença

MIT
