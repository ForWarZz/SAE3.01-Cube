<?php

namespace App\Http\Controllers;

use App\Models\Adresse;
use App\Models\Ville;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdresseController extends Controller
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
        ]);
    }

    /**
     * Store a newly created address in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $client = Auth::user();

        $validated = $request->validate([
            'alias_adresse' => ['required', 'string', 'max:255'],
            'nom_adresse' => ['required', 'string', 'max:255'],
            'prenom_adresse' => ['required', 'string', 'max:255'],
            'telephone_adresse' => ['required', 'string', 'max:20'],
            'tel_mobile_adresse' => ['nullable', 'string', 'max:20'],
            'societe_adresse' => ['nullable', 'string', 'max:255'],
            'tva_adresse' => ['nullable', 'string', 'max:50'],
            'num_voie_adresse' => ['required', 'string', 'max:10'],
            'rue_adresse' => ['required', 'string', 'max:255'],
            'complement_adresse' => ['nullable', 'string', 'max:255'],
            'code_postal' => ['required', 'string', 'max:10'],
            'nom_ville' => ['required', 'string', 'max:255'],
        ]);

        // Find or create the city
        $ville = Ville::firstOrCreate(
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
