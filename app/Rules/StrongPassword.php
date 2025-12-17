<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (strlen($value) < 12) {
            $fail('Le mot de passe doit contenir au moins 12 caractères.');

            return;
        }

        if (! preg_match('/[a-z]/', $value)) {
            $fail('Le mot de passe doit contenir au moins une minuscule.');

            return;
        }

        if (! preg_match('/[A-Z]/', $value)) {
            $fail('Le mot de passe doit contenir au moins une majuscule.');

            return;
        }

        if (! preg_match('/\d/', $value)) {
            $fail('Le mot de passe doit contenir au moins un chiffre.');

            return;
        }

        if (! preg_match('/[@$!%*?&#]/', $value)) {
            $fail('Le mot de passe doit contenir au moins un caractère spécial (@$!%*?&#).');
        }
    }
}
