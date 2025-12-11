<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommercialCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')->orderBy('id_categorie', 'desc')->paginate(10);
        $allCategories = Category::all();
        $allCategories = $allCategories->sortBy(function ($cat) {
            return $cat->getFullPath();
        }, SORT_NATURAL | SORT_FLAG_CASE);

        return view('commercial.categories', compact('categories', 'allCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_categorie' => [
                'required',
                'string',
                'max:50',
                Rule::unique('categorie')->where(function ($query) use ($request) {
                    return $query->where('id_categorie_parent', $request->id_categorie_parent);
                }),
            ],
            'id_categorie_parent' => 'nullable|exists:categorie,id_categorie',
        ], [
            'nom_categorie.unique' => 'Cette catégorie existe déjà à cet endroit (même parent).',
        ]);

        Category::create([
            'nom_categorie' => $request->nom_categorie,
            'id_categorie_parent' => $request->id_categorie_parent,
        ]);

        return redirect()->back();
    }
}
