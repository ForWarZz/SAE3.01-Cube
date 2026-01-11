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
        $path = $this->saveInvoiceInStorage($invoice);

        return $client->registeredBikes()->create([
            'id_magasin' => $shopId,
            'num_serie_velo_enr' => $serialNumber,
            'date_achat_velo_enr' => $purchaseDate,
            'millesime_velo_enr' => $bikeYear,
            'chemin_facture_velo_enr' => $path,
        ]);
    }

    public function updateRegisteredBike(BikeRegistered $bikeRegistered, int $shopId, ?string $serialNumber, ?string $purchaseDate, ?string $bikeYear, ?UploadedFile $invoice): BikeRegistered
    {
        if ($invoice === null) {
            $bikeRegistered->update([
                'id_magasin' => $shopId,
                'num_serie_velo_enr' => $serialNumber,
                'date_achat_velo_enr' => $purchaseDate,
                'millesime_velo_enr' => $bikeYear,
            ]);

            return $bikeRegistered;
        }

        Storage::disk('private')->delete($bikeRegistered->chemin_facture_velo_enr);
        $path = $this->saveInvoiceInStorage($invoice);

        $bikeRegistered->update([
            'id_magasin' => $shopId,
            'num_serie_velo_enr' => $serialNumber,
            'date_achat_velo_enr' => $purchaseDate,
            'millesime_velo_enr' => $bikeYear,
            'chemin_facture_velo_enr' => $path,
        ]);

        return $bikeRegistered;
    }

    public function downloadRegisteredInvoice(BikeRegistered $bikeRegistered): StreamedResponse
    {
        return Storage::disk('private')->download($bikeRegistered->chemin_facture_velo_enr);
    }

    public function saveInvoiceInStorage(UploadedFile $invoice): string
    {
        $filename = Str::uuid().'.pdf';
        $path = $invoice->storeAs(
            'factures',
            $filename,
            'private'
        );

        return $path;
    }
}
