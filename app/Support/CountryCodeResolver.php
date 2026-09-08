<?php

namespace App\Support;

use Illuminate\Support\Str;

class CountryCodeResolver
{
    /** @var array<string, string> */
    private const ALIASES = [
        'mozambique' => 'MZ',
        'mocambique' => 'MZ',
        'south africa' => 'ZA',
        'africa do sul' => 'ZA',
        'portugal' => 'PT',
        'brazil' => 'BR',
        'brasil' => 'BR',
    ];

    public static function resolve(?string $country): ?string
    {
        if ($country === null) {
            return null;
        }

        $trimmed = trim($country);

        if (strlen($trimmed) === 2 && ctype_alpha($trimmed)) {
            return strtoupper($trimmed);
        }

        $normalized = self::normalize($trimmed);

        return self::ALIASES[$normalized] ?? null;
    }

    private static function normalize(string $value): string
    {
        return strtolower(trim(Str::ascii($value)));
    }
}
