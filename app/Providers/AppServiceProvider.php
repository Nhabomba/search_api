<?php

namespace App\Providers;

use App\Contracts\GeocodingServiceInterface;
use App\Services\Geocoding\OpenMeteoGeocodingService;
use App\Services\Logging\WeatherConsultationLogger;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Mesma instância partilhada entre serviços e middleware.
        $this->app->singleton(WeatherConsultationLogger::class);

        $this->app->bind(GeocodingServiceInterface::class, OpenMeteoGeocodingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
