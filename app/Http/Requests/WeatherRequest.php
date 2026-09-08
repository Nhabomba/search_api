<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Valida os parâmetros da consulta de clima (city obrigatório, country opcional).
class WeatherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'city' => ['required', 'string', 'min:1', 'max:255'],
            'country' => ['nullable', 'string', 'min:1', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'city.required' => 'O parâmetro city é obrigatório.',
            'city.string' => 'O parâmetro city deve ser um texto.',
            'country.string' => 'O parâmetro country deve ser um texto.',
        ];
    }

    public function city(): string
    {
        return trim($this->validated('city'));
    }

    public function country(): ?string
    {
        $country = $this->validated('country');

        return $country !== null ? trim($country) : null;
    }
}
