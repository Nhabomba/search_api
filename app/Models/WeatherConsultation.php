<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherConsultation extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'requested_city',
        'requested_country',
        'city',
        'country',
        'country_code',
        'admin1',
        'latitude',
        'longitude',
        'timezone',
        'weather_time',
        'consulted_at',
        'current',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'consulted_at' => 'datetime',
            'current' => 'array',
        ];
    }
}
