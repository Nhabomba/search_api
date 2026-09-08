<?php

namespace App\Support;

use App\DataTransferObjects\CityLocation;
use Illuminate\Support\Str;

class CountryMatcher
{
    public static function matches(CityLocation $location, string $requestedCountry): bool
    {
        $requestedCode = CountryCodeResolver::resolve($requestedCountry);

        if ($requestedCode !== null && strtoupper($location->countryCode) === $requestedCode) {
            return true;
        }

        $requested = self::normalize($requestedCountry);
        $returned = self::normalize($location->country);

        if ($requested === $returned) {
            return true;
        }

        return str_contains($returned, $requested) || str_contains($requested, $returned);
    }

    private static function normalize(string $value): string
    {
        return strtolower(trim(Str::ascii($value)));
    }
}
