<?php

namespace App\Http\Controllers;

use App\Models\Shop;

class BikeRegisterController extends Controller
{
    public function index()
    {
        $client = auth()->user()->load('registeredBikes');
        $registeredBikes = $client->registeredBikes;

        return view('dashboard.bike-registered.index', [
            'bikes' => $registeredBikes,
        ]);
    }

    public function create()
    {
        $shops = Shop::with('city')->get();

        return view('dashboard.bike-registered.create', [
            'shops' => $shops,
        ]);
    }

    public function store() {}

    public function destroy() {}
}
