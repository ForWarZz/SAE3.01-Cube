<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderReturnCreateRequest extends FormRequest
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
            'message' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.line_id' => ['required', 'integer', 'exists:ligne_commande,id_ligne'],
            'items.*.quantity' => ['required', 'integer', 'min:0'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp'],
        ];
    }

    public function messages(): array
    {
        return [
            'message.string' => 'Le message doit être une chaîne de caractères.',
            'message.max' => 'Le message ne peut pas dépasser 1000 caractères.',

            'items.required' => 'Les articles sont requis.',
            'items.array' => 'Le format des articles est invalide.',
            'items.min' => 'Au moins un article doit être fourni.',
            'items.*.line_id.required' => "L'identifiant de la ligne est requis.",
            'items.*.line_id.integer' => "L'identifiant de la ligne doit être un entier.",
            'items.*.line_id.exists' => "La ligne de commande spécifiée n'existe pas.",
            'items.*.quantity.required' => 'La quantité est requise.',
            'items.*.quantity.integer' => 'La quantité doit être un entier.',
            'items.*.quantity.min' => 'La quantité ne peut pas être négative.',

            'attachments.max' => 'Vous ne pouvez joindre que 5 fichiers maximum.',
            'attachments.*.file' => 'Le fichier doit être valide.',
            'attachments.*.max' => 'Chaque fichier ne peut pas dépasser 10 Mo.',
            'attachments.*.mimes' => 'Seuls les fichiers PDF, JPG, PNG et WEBP sont acceptés.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            $hasAtLeastOne = collect($items)->some(fn ($item) => ($item['quantity'] ?? 0) > 0);

            if (! $hasAtLeastOne) {
                $validator->errors()->add('items', 'Vous devez sélectionner au moins un article à retourner (quantité > 0).');
            }
        });
    }
}
