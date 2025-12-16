<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class AccountDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        $rules = [
            'confirmation' => ['required', 'accepted'],
        ];

        if (! $user->google_id) {
            $rules['password'] = ['required', 'string'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'confirmation.required' => 'Vous devez confirmer la suppression du compte.',
            'confirmation.accepted' => 'Vous devez accepter la suppression du compte.',
            'password.required' => 'Le mot de passe est obligatoire pour supprimer le compte.',
        ];
    }

    public function validateCurrentPassword(): bool
    {
        return Hash::check($this->password, $this->user()->hash_mdp_client);
    }
}
