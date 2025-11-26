<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\ModeleVelo;
use App\Models\Velo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Ramsey\Collection\Collection;

class VeloController extends Controller
{
    public function index(){
        return view("velo-list", ['velos'=>Velo::query()->orderBy('id_article', 'asc')->paginate(15)]);
    }
}
