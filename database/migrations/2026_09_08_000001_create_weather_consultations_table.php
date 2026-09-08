<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weather_consultations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('requested_city');
            $table->string('requested_country')->nullable();
            $table->string('city');
            $table->string('country');
            $table->string('country_code', 2);
            $table->string('admin1')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('timezone');
            $table->string('weather_time');
            $table->timestamp('consulted_at');
            $table->json('current');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_consultations');
    }
};
