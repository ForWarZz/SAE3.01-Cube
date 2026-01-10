<?php

namespace App\Http\Requests\Technical;

use Illuminate\Foundation\Http\FormRequest;

class AddGeometryCharacteristicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action_type' => ['required', 'in:existing,new'],
            'existing_id' => ['required_if:action_type,existing', 'nullable', 'exists:caracteristique_geometrie,id_carac_geo'],
            'new_name' => ['required_if:action_type,new', 'nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'action_type.required' => 'Le type d\'action est obligatoire.',
            'action_type.in' => 'Le type d\'action doit être "existing" ou "new".',
            'existing_id.required_if' => 'Vous devez sélectionner une caractéristique existante.',
            'existing_id.exists' => 'La caractéristique sélectionnée n\'existe pas.',
            'new_name.required_if' => 'Vous devez entrer un nom pour la nouvelle caractéristique.',
            'new_name.string' => 'Le nom de la caractéristique doit être une chaîne de caractères.',
            'new_name.max' => 'Le nom de la caractéristique ne peut pas dépasser 255 caractères.',
        ];
    }
}
