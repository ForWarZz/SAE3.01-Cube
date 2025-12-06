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
                'regex:/[@$!%*?&#]/',  // au moins un caractère spécial
            ],
            'privacy_policy' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'L\'adresse email doit être une adresse email valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',

            'naissance_client.before' => 'Vous devez avoir au moins 18 ans pour vous inscrire.',

            'password.regex' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
            'password.min' => 'Le mot de passe doit contenir au moins 12 caractères.',
            'password.confirmed' => 'Les deux mots de passe ne correspondent pas.',

            'privacy_policy.required' => 'Vous devez accepter la politique de confidentialité pour continuer.',
            'privacy_policy.accepted' => 'Vous devez accepter la politique de confidentialité pour créer un compte.',
        ];
    }
}
