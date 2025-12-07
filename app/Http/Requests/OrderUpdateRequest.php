<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
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
            'shipping_id' => 'nullable|exists:moyen_livraison,id_moyen_livraison',
            'billing_id' => 'nullable|exists:adresse,id_adresse',
            'delivery_id' => 'nullable|exists:adresse,id_adresse',
        ];
    }
}
