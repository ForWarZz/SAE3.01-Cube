<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Models\Address;
use App\Models\City;
use App\Services\GdprService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AddressController extends Controller
{
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

        //        // Find or create the city
        //        $ville = City::firstOrCreate(
        //            ['cp_ville' => $validated['code_postal'], 'nom_ville' => $validated['nom_ville']],
        //            ['cp_ville' => $validated['code_postal'], 'nom_ville' => $validated['nom_ville'], 'pays_ville' => 'France']
        //        );

        try {
            $ville = City::firstOrCreate(
                [
                    'cp_ville' => $validated['code_postal'],
                    'nom_ville' => $validated['nom_ville'],
                ],
                [
                    'pays_ville' => 'France',
                ]
            );
        } catch (QueryException $e) {
            $ville = City::where('cp_ville', $validated['code_postal'])
                ->where('nom_ville', $validated['nom_ville'])
                ->firstOrFail();
        }
        Address::create([
            'id_client' => $client->id_client,
            'id_ville' => $ville->id_ville,
            'alias_adresse' => $validated['alias_adresse'],
            'nom_adresse' => $validated['nom_adresse'],
            'prenom_adresse' => $validated['prenom_adresse'],
            'telephone_adresse' => $validated['telephone_adresse'],
            'tel_mobile_adresse' => $validated['tel_mobile_adresse'] ?? null,
            'societe_adresse' => $validated['societe_adresse'] ?? null,
            'tva_adresse' => $validated['tva_adresse'] ?? null,
            'num_voie_adresse' => $validated['num_voie_adresse'],
            'rue_adresse' => $validated['rue_adresse'],
            'complement_adresse' => $validated['complement_adresse'] ?? null,
        ]);

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

    public function update(AddressUpdateRequest $request, Address $adresse): RedirectResponse
    {
        $client = Auth::user();

        if ($adresse->id_client !== $client->id_client) {
            abort(403);
        }

        $validated = $request->validated();

        try {
            $ville = City::firstOrCreate(
                [
                    'cp_ville' => $validated['code_postal'],
                    'nom_ville' => $validated['nom_ville'],
                ],
                [
                    'pays_ville' => 'France',
                ]
            );
        } catch (QueryException $e) {
            $ville = City::where('cp_ville', $validated['code_postal'])
                ->where('nom_ville', $validated['nom_ville'])
                ->firstOrFail();
        }

        $adresse->update([
            'id_ville' => $ville->id_ville,
            'alias_adresse' => $validated['alias_adresse'],
            'nom_adresse' => $validated['nom_adresse'],
            'prenom_adresse' => $validated['prenom_adresse'],
            'telephone_adresse' => $validated['telephone_adresse'],
            'tel_mobile_adresse' => $validated['tel_mobile_adresse'] ?? null,
            'societe_adresse' => $validated['societe_adresse'] ?? null,
            'tva_adresse' => $validated['tva_adresse'] ?? null,
            'num_voie_adresse' => $validated['num_voie_adresse'],
            'rue_adresse' => $validated['rue_adresse'],
            'complement_adresse' => $validated['complement_adresse'] ?? null,
        ]);

        return redirect()->route('dashboard.addresses.index')
            ->with('success', 'Adresse modifiée avec succès.');
    }

    /**
     * Remove the specified address from storage.
     * RGPD: Anonymise l'adresse si elle est liée à une commande, sinon la supprime.
     */
    public function destroy(Request $request, Address $adresse, GdprService $gdprService): RedirectResponse
    {
        $client = Auth::user();

        // Ensure the address belongs to the logged-in client
        if ($adresse->id_client !== $client->id_client) {
            abort(403);
        }

        $result = $gdprService->deleteOrAnonymizeAddress($adresse);

        return redirect()->route('dashboard.addresses.index')
            ->with('success', $result['message']);
    }
}
