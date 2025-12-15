<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressUpdateRequest extends FormRequest
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
            'alias_adresse' => ['nullable', 'string', 'max:255'],
            'nom_adresse' => ['required', 'string', 'max:255'],
            'prenom_adresse' => ['required', 'string', 'max:255'],
            'telephone_adresse' => ['required', 'string', 'max:20', 'regex:/^0[1-9](?:[0-9]{2}){4}$/'],
            'tel_mobile_adresse' => ['nullable', 'string', 'max:20', 'regex:/^0[6-7][0-9]{8}$/'],
            'societe_adresse' => ['nullable', 'string', 'max:255'],
            'tva_adresse' => ['nullable', 'string', 'min:13', 'max:13'],
            'num_voie_adresse' => ['required', 'string', 'max:10'],
            'rue_adresse' => ['required', 'string', 'max:255'],
            'complement_adresse' => ['nullable', 'string', 'max:255'],
            'code_postal' => ['required', 'string', 'max:10'],
            'nom_ville' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'alias_adresse.string' => "L'alias doit être une chaîne de caractères.",
            'alias_adresse.max' => "L'alias ne peut pas dépasser 255 caractères.",

            'nom_adresse.required' => 'Le nom est obligatoire.',
            'nom_adresse.string' => 'Le nom doit être une chaîne de caractères.',
            'nom_adresse.max' => 'Le nom ne peut pas dépasser 255 caractères.',

            'prenom_adresse.required' => 'Le prénom est obligatoire.',
            'prenom_adresse.string' => 'Le prénom doit être une chaîne de caractères.',
            'prenom_adresse.max' => 'Le prénom ne peut pas dépasser 255 caractères.',

            'telephone_adresse.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone_adresse.string' => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'telephone_adresse.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
            'telephone_adresse.regex' => 'Le numéro de téléphone doit être un numéro français valide (ex: 0123456789).',

            'tel_mobile_adresse.string' => 'Le numéro de mobile doit être une chaîne de caractères.',
            'tel_mobile_adresse.max' => 'Le numéro de mobile ne peut pas dépasser 20 caractères.',
            'tel_mobile_adresse.regex' => 'Le numéro de mobile doit commencer par 06 ou 07 et contenir 10 chiffres.',

            'societe_adresse.string' => 'Le nom de la société doit être une chaîne de caractères.',
            'societe_adresse.max' => 'Le nom de la société ne peut pas dépasser 255 caractères.',

            'tva_adresse.string' => 'Le numéro de TVA doit être une chaîne de caractères.',
            'tva_adresse.min' => 'Le numéro de TVA doit contenir exactement 13 caractères.',
            'tva_adresse.max' => 'Le numéro de TVA doit contenir exactement 13 caractères.',

            'num_voie_adresse.required' => 'Le numéro de voie est obligatoire.',
            'num_voie_adresse.string' => 'Le numéro de voie doit être une chaîne de caractères.',
            'num_voie_adresse.max' => 'Le numéro de voie ne peut pas dépasser 10 caractères.',

            'rue_adresse.required' => 'Le nom de la rue est obligatoire.',
            'rue_adresse.string' => 'Le nom de la rue doit être une chaîne de caractères.',
            'rue_adresse.max' => 'Le nom de la rue ne peut pas dépasser 255 caractères.',

            'complement_adresse.string' => "Le complément d'adresse doit être une chaîne de caractères.",
            'complement_adresse.max' => "Le complément d'adresse ne peut pas dépasser 255 caractères.",

            'code_postal.required' => 'Le code postal est obligatoire.',
            'code_postal.string' => 'Le code postal doit être une chaîne de caractères.',
            'code_postal.max' => 'Le code postal ne peut pas dépasser 10 caractères.',

            'nom_ville.required' => 'Le nom de la ville est obligatoire.',
            'nom_ville.string' => 'Le nom de la ville doit être une chaîne de caractères.',
            'nom_ville.max' => 'Le nom de la ville ne peut pas dépasser 255 caractères.',
        ];
    }
}
