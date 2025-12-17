<?php

namespace App\Http\Requests;

use App\Rules\StrongPassword;
use Illuminate\Foundation\Http\FormRequest;

class PasswordResetRequest extends FormRequest
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
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', new StrongPassword],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'Le jeton de réinitialisation est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.string' => 'L\'adresse email doit être une chaîne de caractères.',
            'email.email' => 'L\'adresse email doit être une adresse email valide.',
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.confirmed' => 'Les deux mots de passe ne correspondent pas.',
        ];
    }
}
