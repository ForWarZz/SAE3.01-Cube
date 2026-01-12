<?php

namespace App\Http\Controllers;

use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        return view('index', [
            'bikeCategoryId' => Category::BIKE_CATEGORY_ID,
            'accessoryCategoryId' => Category::ACCESSORY_CATEGORY_ID,
        ]);
    }
}
