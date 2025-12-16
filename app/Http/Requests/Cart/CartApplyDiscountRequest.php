<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class CartApplyDiscountRequest extends FormRequest
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
            'discount_code' => [
                'required',
                'string',
                'exists:code_promo,label_code_promo',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'discount_code.required' => 'Le code promo est obligatoire.',
            'discount_code.string' => 'Le code promo doit être une chaîne de caractères.',
            'discount_code.exists' => 'Le code promo est invalide ou inactif.',
        ];
    }
}
