<?php

namespace App\Http\Requests\Bike;

use Illuminate\Foundation\Http\FormRequest;

class BikeCreateRequest extends FormRequest
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
        $isVae = $this->boolean('is_vae');

        $rules = [
            'model_choice' => ['required', 'in:new,existing'],
            'new_model_name' => ['nullable', 'required_if:model_choice,new', 'string', 'max:255'],
            'id_modele_velo' => ['nullable', 'required_if:model_choice,existing', 'integer'],

            'nom_article' => ['required', 'string', 'max:255'],
            'resumer_article' => ['required', 'string', 'max:255'],
            'description_article' => ['required', 'string'],
            'prix_article' => ['required', 'numeric', 'min:0'],
            'pourcentage_remise' => ['nullable', 'integer', 'min:0', 'max:100'],

            'id_categorie' => ['required', 'integer'],
            'id_materiau_cadre' => ['required', 'integer'],
            'id_millesime' => ['required', 'integer'],
            'id_usage' => ['required', 'integer'],
            'is_vae' => ['required', 'boolean'],
        ];

        if ($isVae) {
            $rules['id_type_vae'] = ['required', 'integer', 'exists:type_vae,id_type_vae'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'model_choice.required' => 'Le choix du modèle est obligatoire.',
            'model_choice.in' => 'Le choix du modèle doit être "new" ou "existing".',

            'new_model_name.required_if' => 'Le nom du nouveau modèle est obligatoire lorsque vous créez un nouveau modèle.',
            'id_modele_velo.required_if' => 'Veuillez sélectionner un modèle existant lorsque cette option est choisie.',

            'nom_article.required' => "Le nom de l'article est obligatoire.",
            'nom_article.string' => "Le nom de l'article doit être une chaîne de caractères.",
            'nom_article.max' => "Le nom de l'article ne peut pas dépasser 255 caractères.",

            'resumer_article.required' => "Le résumé de l'article est obligatoire.",
            'resumer_article.string' => 'Le résumé doit être une chaîne de caractères.',
            'resumer_article.max' => 'Le résumé ne peut pas dépasser 255 caractères.',

            'description_article.required' => 'La description de l’article est obligatoire.',
            'description_article.string' => 'La description doit être une chaîne de caractères.',

            'prix_article.required' => 'Le prix est obligatoire.',
            'prix_article.numeric' => 'Le prix doit être un nombre.',
            'prix_article.min' => 'Le prix doit être supérieur ou égal à 0.',

            'pourcentage_remise.integer' => 'Le pourcentage de remise doit être un nombre entier.',
            'pourcentage_remise.min' => 'La remise doit être au minimum de 0%.',
            'pourcentage_remise.max' => 'La remise ne peut pas dépasser 100%.',

            'id_categorie.required' => 'La catégorie est obligatoire.',
            'id_categorie.integer' => 'La catégorie doit être un identifiant valide.',

            'id_materiau_cadre.required' => 'Le matériau du cadre est obligatoire.',
            'id_materiau_cadre.integer' => 'Le matériau du cadre doit être un identifiant valide.',

            'id_millesime.required' => 'Le millésime est obligatoire.',
            'id_millesime.integer' => 'Le millésime doit être un identifiant valide.',

            'id_usage.required' => "L'usage est obligatoire.",
            'id_usage.integer' => "L'usage doit être un identifiant valide.",

            'references.required' => 'Vous devez ajouter au moins une référence.',
            'references.array' => 'Les références doivent être au format tableau.',

            'references.*.id_cadre_velo.required' => 'Le cadre est obligatoire pour chaque référence.',
            'references.*.id_cadre_velo.integer' => "L'identifiant du cadre doit être valide.",

            'references.*.id_couleur.required' => 'La couleur est obligatoire pour chaque référence.',
            'references.*.id_couleur.integer' => "L'identifiant de la couleur doit être valide.",

            'references.*.sizes.required' => 'Chaque référence doit contenir au moins une taille.',
            'references.*.sizes.array' => 'Les tailles doivent être un tableau.',
            'references.*.sizes.min' => 'Chaque référence doit avoir au moins une taille.',

            'is_vae.required' => 'Veuillez indiquer si c\'est un vélo électrique.',
            'is_vae.boolean' => 'La valeur VAE doit être vrai ou faux.',

            'id_type_vae.required' => 'Le type de VAE est obligatoire pour les vélos électriques.',
            'id_type_vae.integer' => 'L\'identifiant du type de VAE doit être valide.',
            'id_type_vae.exists' => 'Le type de VAE sélectionné n\'existe pas.',
        ];
    }
}
