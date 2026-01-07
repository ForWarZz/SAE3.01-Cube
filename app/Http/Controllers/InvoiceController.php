<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download(Order $order)
    {
        $client = auth()->user();

        if ($order->id_client !== $client->id_client) {
            abort(403, 'Accès non autorisé à cette facture.');
        }

        $order->load([
            'items.reference.article',
            'items.reference.bikeReference.color',
            'items.reference.accessory',
            'items.size',
            'billingAddress.city',
            'deliveryAddress.city',
            'shop.city',
            'shippingMode',
            'paymentType',
        ]);

        $pdf = Pdf::loadView('pdf.invoice', [
            'order' => $order,
            'client' => $client,
        ]);

        return $pdf->download("facture-{$order->num_commande}.pdf");
    }
}