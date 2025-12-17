<?php

namespace App\Http\Requests\Bike;

use Illuminate\Foundation\Http\FormRequest;

class BikeModelCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom_modele_velo' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom_modele_velo.required' => 'Le nom du modèle de vélo est obligatoire.',
            'nom_modele_velo.string' => 'Le nom du modèle de vélo doit être une chaîne de caractères.',
            'nom_modele_velo.max' => 'Le nom du modèle de vélo ne peut pas dépasser 255 caractères.',
        ];
    }
}
