<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'civilite' => ['required', 'string', 'in:M,F'],
            'nom_client' => ['required', 'string', 'max:255'],
            'prenom_client' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:client,email_client'],
            'naissance_client' => ['nullable', 'date', 'before:'.now()->subYears(18)->format('Y-m-d')],
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:12',
                'regex:/[a-z]/',      // au moins une minuscule
                'regex:/[A-Z]/',      // au moins une majuscule
                'regex:/[0-9]/',      // au moins un chiffre
                'regex:/[@$!%*?&#]/', // au moins un caractère spécial
            ],
            'privacy_policy' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'civilite.required' => 'La civilité est obligatoire.',
            'civilite.string' => 'La civilité doit être une chaîne de caractères.',
            'civilite.in' => 'La civilité doit être "M" ou "F".',

            'nom_client.required' => 'Le nom est obligatoire.',
            'nom_client.string' => 'Le nom doit être une chaîne de caractères.',
            'nom_client.max' => 'Le nom ne peut pas dépasser 255 caractères.',

            'prenom_client.required' => 'Le prénom est obligatoire.',
            'prenom_client.string' => 'Le prénom doit être une chaîne de caractères.',
            'prenom_client.max' => 'Le prénom ne peut pas dépasser 255 caractères.',

            'email.required' => 'L\'adresse email est obligatoire.',
            'email.string' => 'L\'adresse email doit être une chaîne de caractères.',
            'email.email' => 'L\'adresse email doit être une adresse email valide.',
            'email.max' => 'L\'adresse email ne peut pas dépasser 255 caractères.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',

            'naissance_client.date' => 'La date de naissance doit être une date valide.',
            'naissance_client.before' => 'Vous devez avoir au moins 18 ans pour vous inscrire.',

            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit contenir au moins 12 caractères.',
            'password.confirmed' => 'Les deux mots de passe ne correspondent pas.',
            'password.regex' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',

            'privacy_policy.required' => 'Vous devez accepter la politique de confidentialité pour continuer.',
            'privacy_policy.accepted' => 'Vous devez accepter la politique de confidentialité pour créer un compte.',
        ];
    }
}
