<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\AccountDeleteRequest;
use App\Http\Requests\Account\PasswordUpdateRequest;
use App\Http\Requests\Account\ProfileUpdateRequest;
use App\Services\GdprService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('dashboard.profile.show', [
            'client' => $request->user(),
        ]);
    }

    public function edit(Request $request): View
    {
        return view('dashboard.profile.edit', [
            'client' => $request->user(),
        ]);
    }

    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $client = $request->user();

        if ($client->google_id) {
            return back()->withErrors([
                'password' => 'Vous êtes connecté avec Google. Gérez votre mot de passe via votre compte Google.',
            ]);
        }

        if (! $request->validateCurrentPassword()) {
            return back()->withErrors([
                'current_password' => 'Le mot de passe actuel est incorrect.',
            ]);
        }

        $client->update([
            'hash_mdp_client' => Hash::make($request->password),
        ]);

        return redirect()->route('dashboard.profile.show')
            ->with('success', 'Mot de passe modifié avec succès.');
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $client = $request->user();
        $validated = $request->validated();

        $client->update($validated);

        return redirect()->route('dashboard.profile.show')
            ->with('success', 'Profil mis à jour avec succès.');
    }

    public function destroy(AccountDeleteRequest $request, GdprService $gdprService): RedirectResponse
    {
        $client = $request->user();

        if (! $client->google_id && ! $request->validateCurrentPassword($client)) {
            return back()->withErrors(['password' => 'Le mot de passe est incorrect.']);
        }

        $message = $gdprService->deleteOrAnonymizeClient($client);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', $message);
    }

    public function exportData(Request $request, GdprService $gdprService)
    {
        $client = $request->user();
        $client->load([
            'addresses.city',
            'orders.items.reference.accessory.article',
            'orders.items.reference.bikeReference.article.bike.bikeModel',
            'orders.billingAddress.city',
            'orders.deliveryAddress.city',
        ]);

        $data = $gdprService->exportClientData($client);

        $filename = 'mes-donnees-personnelles-'.now()->format('Y-m-d').'.json';

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
