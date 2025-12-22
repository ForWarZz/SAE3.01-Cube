<?php

namespace App\Http\Requests\Accessory;

use Illuminate\Foundation\Http\FormRequest;

class AccessoryUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom_article' => ['required', 'string', 'max:255'],
            'resumer_article' => ['required', 'string', 'max:255'],
            'description_article' => ['required', 'string'],

            'prix_article' => ['required', 'numeric', 'min:0'],
            'pourcentage_remise' => ['nullable', 'integer', 'min:0', 'max:100'],

            'id_categorie' => ['required', 'integer', 'exists:categorie,id_categorie'],

            'id_matiere_accessoire' => ['required', 'integer', 'exists:matiere_accessoire,id_matiere_accessoire'],

            'sizes' => ['required', 'array', 'min:1'],
            'sizes.*' => ['integer', 'exists:taille,id_taille'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom_article.required' => "Le nom de l'article est obligatoire.",
            'nom_article.string' => "Le nom de l'article doit être une chaîne de caractères.",
            'nom_article.max' => "Le nom de l'article ne peut pas dépasser 255 caractères.",

            'resumer_article.required' => "Le résumé de l'article est obligatoire.",
            'resumer_article.string' => "Le résumé de l'article doit être une chaîne de caractères.",
            'resumer_article.max' => "Le résumé de l'article ne peut pas dépasser 255 caractères.",

            'description_article.required' => "La description de l'article est obligatoire.",
            'description_article.string' => "La description de l'article doit être une chaîne de caractères.",

            'prix_article.required' => "Le prix de l'article est obligatoire.",
            'prix_article.numeric' => "Le prix de l'article doit être un nombre.",
            'prix_article.min' => "Le prix de l'article ne peut pas être négatif.",

            'pourcentage_remise.integer' => 'Le pourcentage de remise doit être un entier.',
            'pourcentage_remise.min' => 'Le pourcentage de remise ne peut pas être inférieur à 0%.',
            'pourcentage_remise.max' => 'Le pourcentage de remise ne peut pas dépasser 100%.',

            'id_categorie.required' => 'La catégorie est obligatoire.',
            'id_categorie.integer' => "L'identifiant de la catégorie doit être un entier.",
            'id_categorie.exists' => "La catégorie spécifiée n'existe pas.",

            'id_matiere_accessoire.required' => 'La matière est obligatoire.',
            'id_matiere_accessoire.integer' => "L'identifiant de la matière doit être un entier.",
            'id_matiere_accessoire.exists' => "La matière spécifiée n'existe pas.",

            'sizes.required' => 'Les tailles sont obligatoires.',
            'sizes.array' => 'Les tailles doivent être un tableau.',
            'sizes.min' => 'Au moins une taille doit être sélectionnée.',
            'sizes.*.integer' => 'Chaque identifiant de taille doit être un entier.',
            'sizes.*.exists' => "Une des tailles sélectionnées n'existe pas.",
        ];
    }
}
