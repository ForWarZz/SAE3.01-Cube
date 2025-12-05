<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressCreateRequest extends FormRequest
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
            'alias_adresse' => ['required', 'string', 'max:255'],
            'nom_adresse' => ['required', 'string', 'max:255'],
            'prenom_adresse' => ['required', 'string', 'max:255'],
            'telephone_adresse' => ['required', 'string', 'max:20', 'regex:/^0[1-9](?:[0-9]{2}){4}$/'],
            'tel_mobile_adresse' => ['nullable', 'string', 'max:20', 'regex:/^0[6-7][0-9]{8}$/'],
            'societe_adresse' => ['nullable', 'string', 'max:255'],
            'tva_adresse' => ['nullable', 'string', 'max:50'],
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
            'telephone_adresse.regex' => 'Le numéro de téléphone doit être un numéro français valide (ex: 0123456789).',
            'tel_mobile_adresse.regex' => 'Le numéro de mobile doit commencer par 06 ou 07 et contenir 10 chiffres.',
        ];
    }
}
