<?php

namespace App\Http\Requests;

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
            'discount_code' => ['required', 'string', 'exists:code_promo,label_code_promo,est_actif,1'],
        ];
    }

    public function messages()
    {
        return [
            'discount_code.exists' => 'Le code promo est invalide ou inactif.',
        ];
    }
}
