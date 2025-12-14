<?php

namespace App\Http\Requests\Bike;

use Illuminate\Foundation\Http\FormRequest;

class BikeReferenceRequest extends FormRequest
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
        // Déterminer si c'est un VAE en fonction du vélo parent
        $bike = $this->route('bike');
        $isVae = false;

        if ($bike) {
            $isVae = $bike->references()
                ->whereHas('ebike')
                ->exists();
        }

        $rules = [
            'numero_reference' => 'required|integer|min:0|unique:reference_article,id_reference',
            'id_cadre_velo' => 'required|integer|exists:cadre_velo,id_cadre_velo',
            'id_couleur' => 'required|integer|exists:couleur,id_couleur',
            'sizes' => 'required|array|min:1',
            'sizes.*' => 'integer|exists:taille,id_taille',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        if ($isVae) {
            $rules['id_batterie'] = 'required|integer|exists:batterie,id_batterie';
        } else {
            $rules['id_batterie'] = 'nullable|integer|exists:batterie,id_batterie';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'numero_reference.unique' => 'Ce numéro de référence est déjà utilisé.',
            'numero_reference.integer' => 'Le numéro de référence doit être un entier.',
            'numero_reference.min' => 'Le numéro de référence doit être positif.',
            'numero_reference.required' => 'Le numéro de référence est obligatoire.',

            'id_cadre_velo.required' => 'Le cadre est obligatoire.',
            'id_cadre_velo.integer' => "L'identifiant du cadre doit être valide.",
            'id_cadre_velo.exists' => 'Le cadre sélectionné n\'existe pas.',

            'id_couleur.required' => 'La couleur est obligatoire.',
            'id_couleur.integer' => "L'identifiant de la couleur doit être valide.",
            'id_couleur.exists' => 'La couleur sélectionnée n\'existe pas.',

            'sizes.required' => 'Vous devez sélectionner au moins une taille.',
            'sizes.array' => 'Les tailles doivent être un tableau.',
            'sizes.min' => 'Vous devez sélectionner au moins une taille.',
            'sizes.*.integer' => "L'identifiant de la taille doit être valide.",
            'sizes.*.exists' => 'La taille sélectionnée n\'existe pas.',

            'id_batterie.required' => 'La batterie est obligatoire pour un vélo électrique.',
            'id_batterie.integer' => 'L\'identifiant de la batterie doit être valide.',
            'id_batterie.exists' => 'La batterie sélectionnée n\'existe pas.',

            'images.array' => 'Les images doivent être un tableau.',
            'images.max' => 'Vous ne pouvez pas ajouter plus de 5 images.',
            'images.*.image' => 'Le fichier doit être une image.',
            'images.*.mimes' => 'L\'image doit être au format JPEG, PNG, JPG ou WebP.',
            'images.*.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ];
    }
}
