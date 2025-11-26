<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return view("index", ['categories' => Category::all()->where('id_categorie_parent', '=', null)]);
    }
}
