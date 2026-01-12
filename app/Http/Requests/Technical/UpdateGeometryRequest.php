<?php

namespace App\Http\Requests\Technical;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeometryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'geo' => ['nullable', 'array'],
            'geo.*' => ['required', 'array'],
            'geo.*.*' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'geo.array' => 'Les données de géométrie doivent être un tableau.',
            'geo.*.array' => 'Chaque caractéristique doit contenir un tableau de valeurs.',
        ];
    }
}
