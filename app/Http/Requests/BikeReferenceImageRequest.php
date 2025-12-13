<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BikeReferenceImageRequest extends FormRequest
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
            'images' => ['required', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'images.required' => 'Au moins une image est requise.',
            'images.array' => 'Les images doivent être fournies sous forme de tableau.',
            'images.max' => 'Vous ne pouvez télécharger qu\'un maximum de 5 images.',
            'images.*.image' => 'Chaque fichier doit être une image valide.',
            'images.*.mimes' => 'Les images doivent être au format jpeg, png, jpg ou webp.',
            'images.*.max' => 'Chaque image ne doit pas dépasser 2 Mo.',
        ];
    }
}
