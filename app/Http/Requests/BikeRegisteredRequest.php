<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BikeRegisteredRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_magasin' => ['required', 'integer', 'exists:magasin,id_magasin'],
            'num_serie_velo_enr' => ['nullable', 'string', 'max:100', 'unique:velo_enregistre,num_serie_velo_enr'],
            'date_achat_velo_enr' => ['nullable', 'date', 'before_or_equal:today'],
            'millesime_velo_enr' => ['nullable', 'string', 'max:4'],
            'facture' => ['required', 'file', 'mimes:pdf', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_magasin.required' => 'Le magasin est obligatoire.',
            'id_magasin.integer' => 'Le magasin doit être un entier valide.',
            'id_magasin.exists' => 'Le magasin sélectionné est invalide.',

            'num_serie_velo_enr.string' => 'Le numéro de série doit être une chaîne de caractères.',
            'num_serie_velo_enr.max' => 'Le numéro de série ne doit pas dépasser 100 caractères.',
            'num_serie_velo_enr.unique' => 'Ce numéro de série est déjà enregistré.',

            'date_achat_velo_enr.date' => "La date d'achat doit être une date valide.",
            'date_achat_velo_enr.before_or_equal' => "La date d'achat ne peut pas être dans le futur.",

            'facture.required' => 'La facture est obligatoire.',
            'facture.file' => 'La facture doit être un fichier valide.',
            'facture.mimes' => 'La facture doit être au format PDF.',
            'facture.max' => 'La taille de la facture ne doit pas dépasser 2 Mo.',
        ];
    }
}
