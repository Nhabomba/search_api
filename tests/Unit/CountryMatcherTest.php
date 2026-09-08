<?php

namespace Tests\Unit;

use App\Support\CountryMatcher;
use Tests\Support\FakeWeatherData;
use Tests\TestCase;

class CountryMatcherTest extends TestCase
{
    public function test_it_matches_country_by_iso_code(): void
    {
        $location = FakeWeatherData::cityLocation();

        $this->assertTrue(CountryMatcher::matches($location, 'MZ'));
        $this->assertTrue(CountryMatcher::matches($location, 'Mozambique'));
        $this->assertTrue(CountryMatcher::matches($location, 'Moçambique'));
    }

    public function test_it_rejects_mismatched_country_for_location(): void
    {
        $location = FakeWeatherData::pretoriaLocation();

        $this->assertFalse(CountryMatcher::matches($location, 'Mozambique'));
        $this->assertFalse(CountryMatcher::matches($location, 'MZ'));
        $this->assertTrue(CountryMatcher::matches($location, 'South Africa'));
        $this->assertTrue(CountryMatcher::matches($location, 'ZA'));
    }
}
