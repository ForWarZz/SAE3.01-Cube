<?php

namespace App\Http\Requests\Staff\DPO;

use Illuminate\Foundation\Http\FormRequest;

class AnonymizeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_threshold' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'date_threshold.required' => 'Veuillez fournir une date.',
            'date_threshold.date' => 'La date fournie est invalide.',
            'date_threshold.before_or_equal' => 'La date ne peut pas être dans le futur.',
        ];
    }
}
