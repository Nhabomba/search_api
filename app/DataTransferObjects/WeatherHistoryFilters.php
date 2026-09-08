<?php

namespace App\DataTransferObjects;

readonly class WeatherHistoryFilters
{
    public function __construct(
        public ?string $city = null,
        public ?string $country = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
    ) {}
}
