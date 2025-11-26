<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categorie;

class CategorieController extends Controller
{
    public function index(){
        return view("index", ['categories'=>Categorie::all()->where('id_categorie_parent', '=', null)]);
    }
}
