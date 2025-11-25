<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Reference;
use App\Models\ReferenceVelo;
use App\Models\Velo;
use App\Services\VeloService;

class VeloController extends Controller
{
    public function __construct(
        protected VeloService $veloService,
    )
    {}

    public function index()
    {

    }

    public function redirectToDefaultVariant($id_article)
    {
        $premiereRef = ReferenceVelo::where('id_article', $id_article)
            ->orderBy('id_reference')
            ->firstOrFail();

        return redirect()->route('velo.show', ['reference' => $premiereRef->id_reference]);
    }

    public function show(ReferenceVelo $reference)
    {
        $data = $this->veloService->prepareViewData($reference);

        return view('article.velo.show', $data);
    }
}
