<?php

namespace App\Contracts;

use App\DataTransferObjects\CityLocation;
use App\Exceptions\GeocodingException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;

interface GeocodingServiceInterface
{
    /**
     * Pesquisa cidades pelo nome e devolve localizações válidas.
     *
     * @return Collection<int, CityLocation>
     *
     * @throws GeocodingException
     * @throws ConnectionException
     */
    public function search(string $name, int $count, ?string $countryCode = null): Collection;
}
