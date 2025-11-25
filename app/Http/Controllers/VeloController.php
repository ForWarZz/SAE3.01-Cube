<?php

namespace App\Http\Controllers;

use App\Models\Velo;
use Illuminate\Http\Request;

class VeloController extends Controller
{
    public function index(){
        return view("velo-list", ['velos'=>Velo::query()->orderBy('id_article', 'asc')->get()]);
    }
}
