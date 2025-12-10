<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Models\Adresse;
use App\Models\City;
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
        $adresses = Adresse::where('id_client', $client->id_client)->with('ville')->get();

        return view('dashboard.adresses.index', [
            'client' => $client,
            'adresses' => $adresses,
        ]);
    }

    /**
     * Show the form for creating a new address.
     */
    public function create(Request $request): View
    {
        $client = Auth::user();

        return view('dashboard.adresses.create', [
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

        // Find or create the city
        $ville = City::firstOrCreate(
            ['cp_ville' => $validated['code_postal'], 'nom_ville' => $validated['nom_ville']],
            ['cp_ville' => $validated['code_postal'], 'nom_ville' => $validated['nom_ville'], 'pays_ville' => 'France']
        );

        Adresse::create([
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

        return redirect()->route('dashboard.adresses.index')
            ->with('success', 'Adresse créée avec succès.');
    }

    /**
     * Remove the specified address from storage.
     */
    public function destroy(Request $request, Adresse $adresse): RedirectResponse
    {
        $client = Auth::user();

        // Ensure the address belongs to the logged-in client
        if ($adresse->id_client !== $client->id_client) {
            abort(403);
        }

        $adresse->delete();

        return redirect()->route('dashboard.adresses.index')
            ->with('success', 'Adresse supprimée avec succès.');
    }
}
