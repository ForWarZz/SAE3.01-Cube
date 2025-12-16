<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\Address\AddressCreateRequest;
use App\Http\Requests\Account\Address\AddressUpdateRequest;
use App\Models\Address;
use App\Services\Commercial\AddressService;
use App\Services\GdprService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function __construct(
        private AddressService $addressService,
    ) {}

    /**
     * Display a listing of the client's addresses.
     */
    public function index(Request $request): View
    {
        $client = Auth::user();
        $adresses = Address::where('id_client', $client->id_client)->with('city')->get();

        return view('dashboard.addresses.index', [
            'client' => $client,
            'addresses' => $adresses,
        ]);
    }

    /**
     * Show the form for creating a new address.
     */
    public function create(Request $request): View
    {
        $client = Auth::user();

        return view('dashboard.addresses.create', [
            'client' => $client,
            'intended' => $request->query('intended'),
        ]);
    }

    /**
     * Store a newly created address in storage.
     */
    public function store(AddressCreateRequest $request): RedirectResponse
    {
        $client = Auth::user();
        $validated = $request->validated();

        $this->addressService->createAddress($client->id_client, $validated);

        // Si un paramètre intended existe, rediriger vers cette URL
        $intended = $request->input('intended');
        if ($intended) {
            return redirect($intended)->with('success', 'Adresse créée avec succès.');
        }

        return redirect()->route('dashboard.addresses.index')
            ->with('success', 'Adresse créée avec succès.');
    }

    public function edit(Address $adresse): View
    {
        $client = Auth::user();

        if ($adresse->id_client !== $client->id_client) {
            abort(403);
        }

        $adresse->load('city');

        return view('dashboard.addresses.edit', [
            'client' => $client,
            'address' => $adresse,
        ]);
    }

    public function update(AddressUpdateRequest $request, Address $adresse, GdprService $gdprService): RedirectResponse
    {
        $client = Auth::user();

        if ($adresse->id_client !== $client->id_client) {
            abort(403);
        }

        $validated = $request->validated();
        $result = $gdprService->updateOrReplaceAddress($adresse, $validated);

        return redirect()->route('dashboard.addresses.index')
            ->with('success', $result['message']);
    }

    public function destroy(Request $request, Address $adresse, GdprService $gdprService): RedirectResponse
    {
        $client = Auth::user();

        if ($adresse->id_client !== $client->id_client) {
            abort(403);
        }

        $message = $gdprService->deleteOrSoftDelete($adresse);

        return redirect()->route('dashboard.addresses.index')
            ->with('success', $message);
    }
}
