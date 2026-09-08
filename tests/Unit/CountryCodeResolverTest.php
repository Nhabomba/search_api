<?php

namespace Tests\Unit;

use App\Support\CountryCodeResolver;
use Tests\TestCase;

class CountryCodeResolverTest extends TestCase
{
    public function test_it_resolves_iso_codes_from_country_names(): void
    {
        $this->assertSame('MZ', CountryCodeResolver::resolve('Mozambique'));
        $this->assertSame('MZ', CountryCodeResolver::resolve('Moçambique'));
        $this->assertSame('ZA', CountryCodeResolver::resolve('South Africa'));
    }

    public function test_it_accepts_two_letter_country_codes(): void
    {
        $this->assertSame('MZ', CountryCodeResolver::resolve('mz'));
        $this->assertSame('ZA', CountryCodeResolver::resolve('ZA'));
    }

    public function test_it_returns_null_for_unknown_countries(): void
    {
        $this->assertNull(CountryCodeResolver::resolve('Atlantis'));
    }
}
