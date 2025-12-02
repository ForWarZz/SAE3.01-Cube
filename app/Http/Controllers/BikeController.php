<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\BikeReference;
use App\Services\BikeService;

class BikeController extends Controller
{
    public function __construct(
        protected BikeService $veloService,
    ) {}

    public function index() {}

    public function redirectToDefaultVariant(Bike $bike)
    {
        $firstReference = BikeReference::where('id_article', $bike->id_article)
            ->orderBy('id_reference')
            ->firstOrFail();

        return redirect()->route('articles.show', ['reference' => $firstReference->id_reference]);
    }
}
