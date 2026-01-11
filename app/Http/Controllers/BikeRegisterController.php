<?php

namespace App\Http\Controllers;

use App\Http\Requests\BikeRegisteredRequest;
use App\Models\BikeRegistered;
use App\Models\Shop;
use App\Services\BikeRegisterService;

class BikeRegisterController extends Controller
{
    public function __construct(
        private readonly BikeRegisterService $bikeRegisterService
    ) {}

    public function index()
    {
        $client = auth()->user();
        $registeredBikes = BikeRegistered::with('shop')
            ->where('id_client', $client->id_client)
            ->paginate(15);

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

    public function store(BikeRegisteredRequest $request)
    {
        $invoice = $request->file('facture');

        $this->bikeRegisterService->registerNewBike(
            shopId: $request->input('id_magasin'),
            serialNumber: $request->input('num_serie_velo_enr'),
            purchaseDate: $request->input('date_achat_velo_enr'),
            bikeYear: $request->input('millesime_velo_enr'),
            invoice: $invoice
        );

        return redirect()->route('dashboard.bike-registered.index')
            ->with('success', 'Vélo enregistré avec succès.');
    }

    public function destroy() {}

    public function downloadInvoice(BikeRegistered $bikeRegistered)
    {
        $client = auth()->user();

        if ($bikeRegistered->id_client !== $client->id_client) {
            abort(403, 'Vous n\'êtes pas autorisé à accéder à cette ressource.');
        }

        return $this->bikeRegisterService->downloadRegisteredInvoice($bikeRegistered);
    }
}
