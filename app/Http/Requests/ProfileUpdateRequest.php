<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'civilite' => ['required', 'string', 'in:Monsieur,Madame'],
            'prenom_client' => ['required', 'string', 'max:255'],
            'nom_client' => ['required', 'string', 'max:255'],
            'email_client' => ['required', 'string', 'email', 'max:255',  Rule::unique(Client::class, 'email_client')->ignore($this->user())],
            'naissance_client' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'civilite.required' => 'Le champ civilité est obligatoire.',
            'civilite.in' => 'Le champ civilité doit être "Monsieur" ou "Madame".',

            'prenom_client.required' => 'Le prénom est obligatoire.',
            'prenom_client.max' => 'Le prénom ne doit pas dépasser 255 caractères.',

            'nom_client.required' => 'Le nom est obligatoire.',
            'nom_client.max' => 'Le nom ne doit pas dépasser 255 caractères.',

            'email_client.required' => "L'adresse e-mail est obligatoire.",
            'email_client.email' => "L'adresse e-mail doit être valide.",
            'email_client.max' => "L'adresse e-mail ne doit pas dépasser 255 caractères.",
            'email_client.unique' => 'Cette adresse e-mail est déjà utilisée.',

            'naissance_client.date' => 'La date de naissance doit être une date valide.',
        ];
    }
}
