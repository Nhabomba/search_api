<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

// Valida filtros do histórico (cidade, país, intervalo de datas).
class WeatherHistoryRequest extends FormRequest
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
            'city' => ['nullable', 'string', 'min:1', 'max:255'],
            'country' => ['nullable', 'string', 'min:1', 'max:255'],
            'startDate' => ['nullable', 'date', 'date_format:Y-m-d'],
            'endDate' => ['nullable', 'date', 'date_format:Y-m-d'],
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'city.string' => 'O parâmetro city deve ser um texto.',
            'country.string' => 'O parâmetro country deve ser um texto.',
            'startDate.date' => 'O parâmetro startDate deve ser uma data válida.',
            'startDate.date_format' => 'O parâmetro startDate deve estar no formato YYYY-MM-DD.',
            'endDate.date' => 'O parâmetro endDate deve ser uma data válida.',
            'endDate.date_format' => 'O parâmetro endDate deve estar no formato YYYY-MM-DD.',
            'page.integer' => 'O parâmetro page deve ser um número inteiro.',
            'page.min' => 'O parâmetro page deve ser no mínimo 1.',
            'perPage.integer' => 'O parâmetro perPage deve ser um número inteiro.',
            'perPage.min' => 'O parâmetro perPage deve ser no mínimo 1.',
            'perPage.max' => 'O parâmetro perPage não pode ser superior a 100.',
        ];
    }

    // Garante que endDate não é anterior a startDate.
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $startDate = $this->input('startDate');
            $endDate = $this->input('endDate');

            if ($startDate && $endDate && $endDate < $startDate) {
                $validator->errors()->add(
                    'endDate',
                    'O parâmetro endDate deve ser igual ou posterior a startDate.',
                );
            }
        });
    }

    public function city(): ?string
    {
        $city = $this->validated('city');

        return $city !== null ? trim($city) : null;
    }

    public function country(): ?string
    {
        $country = $this->validated('country');

        return $country !== null ? trim($country) : null;
    }

    public function startDate(): ?string
    {
        return $this->validated('startDate');
    }

    public function endDate(): ?string
    {
        return $this->validated('endDate');
    }

    public function page(): int
    {
        return (int) ($this->validated('page') ?? 1);
    }

    public function perPage(): int
    {
        return (int) ($this->validated('perPage') ?? config('services.weather_history.per_page', 15));
    }
}
