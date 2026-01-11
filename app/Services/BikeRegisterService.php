<?php

namespace App\Services;

use App\Models\BikeRegistered;
use Illuminate\Http\UploadedFile;
use Storage;
use Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BikeRegisterService
{
    public function registerNewBike(int $shopId, ?string $serialNumber, ?string $purchaseDate, ?string $bikeYear, UploadedFile $invoice): BikeRegistered
    {
        $client = auth()->user();

        $filename = Str::uuid().'.pdf';
        $path = $invoice->storeAs(
            'factures',
            $filename,
            'private'
        );

        return $client->registeredBikes()->create([
            'id_magasin' => $shopId,
            'num_serie_velo_enr' => $serialNumber,
            'date_achat_velo_enr' => $purchaseDate,
            'millesime_velo_enr' => $bikeYear,
            'chemin_facture_velo_enr' => $path,
        ]);
    }

    public function downloadRegisteredInvoice(BikeRegistered $bikeRegistered): StreamedResponse
    {
        return Storage::disk('private')->download($bikeRegistered->chemin_facture_velo_enr);
    }
}
