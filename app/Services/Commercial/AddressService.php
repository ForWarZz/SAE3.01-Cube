<?php

namespace App\Services\Commercial;

use App\Models\Address;
use App\Models\City;
use Illuminate\Database\QueryException;

class AddressService
{
    public function createAddress(int $clientId, array $data): Address
    {
        try {
            $ville = City::firstOrCreate(
                [
                    'cp_ville' => $data['code_postal'],
                    'nom_ville' => $data['nom_ville'],
                ],
                [
                    'pays_ville' => 'France',
                ]
            );
        } catch (QueryException $e) {
            $ville = City::where('cp_ville', $data['code_postal'])
                ->where('nom_ville', $data['nom_ville'])
                ->firstOrFail();
        }

        return Address::create([
            'id_client' => $clientId,
            'id_ville' => $ville->id_ville,
            'alias_adresse' => $data['alias_adresse'],
            'nom_adresse' => $data['nom_adresse'],
            'prenom_adresse' => $data['prenom_adresse'],
            'telephone_adresse' => $data['telephone_adresse'],
            'tel_mobile_adresse' => $data['tel_mobile_adresse'] ?? null,
            'societe_adresse' => $data['societe_adresse'] ?? null,
            'tva_adresse' => $data['tva_adresse'] ?? null,
            'num_voie_adresse' => $data['num_voie_adresse'],
            'rue_adresse' => $data['rue_adresse'],
            'complement_adresse' => $data['complement_adresse'] ?? null,
        ]);
    }
}
