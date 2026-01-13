@php
    use App\Models\ShippingMode;
@endphp

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>Facture {{ $order->num_commande }}</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family:
                    DejaVu Sans,
                    sans-serif;
                font-size: 12px;
                color: #333;
                padding: 40px;
            }

            .logo {
                font-size: 24px;
                font-weight: bold;
                color: #1f2937;
            }

            .invoice-number {
                font-size: 18px;
                font-weight: bold;
                color: #1f2937;
            }

            .address-block {
                background: #f9fafb;
                padding: 15px;
                margin-bottom: 10px;
            }

            .address-title {
                font-weight: bold;
                color: #6b7280;
                font-size: 10px;
                text-transform: uppercase;
                margin-bottom: 8px;
            }

            .items-table {
                width: 100%;
                border-collapse: collapse;
                margin: 30px 0;
            }

            .items-table th {
                background: #f3f4f6;
                padding: 12px;
                text-align: left;
                font-weight: bold;
                border-bottom: 2px solid #e5e7eb;
            }

            .items-table td {
                padding: 12px;
                border-bottom: 1px solid #e5e7eb;
            }

            .text-right {
                text-align: right;
            }

            .totals {
                width: 300px;
                margin-left: auto;
                margin-top: 20px;
            }

            .totals td {
                padding: 8px 0;
            }

            .total-row {
                font-size: 16px;
                font-weight: bold;
                border-top: 2px solid #1f2937;
            }

            .footer {
                margin-top: 50px;
                text-align: center;
                color: #6b7280;
                font-size: 10px;
            }

            .discount {
                color: #059669;
            }
        </style>
    </head>
    <body>
        <table style="width: 100%; margin-bottom: 40px">
            <tr>
                <td style="vertical-align: top">
                    <div class="logo">CUBE FRANCE</div>
                    <p style="color: #6b7280; margin-top: 5px">Vélos et accessoires</p>
                </td>
                <td style="text-align: right; vertical-align: top">
                    <div class="invoice-number">FACTURE</div>
                    <p style="margin-top: 5px">N° {{ $order->num_commande }}</p>
                    <p>Date : {{ $order->date_commande->format("d/m/Y") }}</p>
                    @if ($order->date_paiement)
                        <p>Payée le : {{ $order->date_paiement->format("d/m/Y") }}</p>
                    @endif
                </td>
            </tr>
        </table>

        <table style="width: 100%; margin-bottom: 30px">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 10px">
                    <div class="address-block">
                        <div class="address-title">Adresse de facturation</div>
                        @if ($order->billingAddress)
                            <p>
                                <strong>{{ $order->billingAddress->prenom_adresse }} {{ $order->billingAddress->nom_adresse }}</strong>
                            </p>
                            <p>{{ $order->billingAddress->num_voie_adresse }} {{ $order->billingAddress->rue_adresse }}</p>
                            @if ($order->billingAddress->complement_adresse)
                                <p>{{ $order->billingAddress->complement_adresse }}</p>
                            @endif

                            <p>{{ $order->billingAddress->city->cp_ville }} {{ $order->billingAddress->city->nom_ville }}</p>
                        @endif
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top; padding-left: 10px">
                    <div class="address-block">
                        <div class="address-title">
                            @if ($order->shop)
                                Point de retrait
                            @else
                                Adresse de livraison
                            @endif
                        </div>

                        @if ($order->shop)
                            <p><strong>{{ $order->shop->nom_magasin }}</strong></p>
                            <p>{{ $order->shop->full_address }}</p>
                            <p>{{ $order->shop->city->cp_ville }} {{ $order->shop->city->nom_ville }}</p>
                        @elseif ($order->deliveryAddress)
                            <p>
                                <strong>{{ $order->deliveryAddress->prenom_adresse }} {{ $order->deliveryAddress->nom_adresse }}</strong>
                            </p>
                            <p>{{ $order->deliveryAddress->num_voie_adresse }} {{ $order->deliveryAddress->rue_adresse }}</p>
                            @if ($order->deliveryAddress->complement_adresse)
                                <p>{{ $order->deliveryAddress->complement_adresse }}</p>
                            @endif

                            <p>{{ $order->deliveryAddress->city->cp_ville }} {{ $order->deliveryAddress->city->nom_ville }}</p>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th class="text-right">Qté</th>
                    <th class="text-right">Prix unitaire HT</th>
                    <th class="text-right">Total HT</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subtotalHT = 0;
                @endphp

                @foreach ($order->items as $item)
                    @php
                        $article = $item->reference->article ?? ($item->reference->accessory ?? null);
                        $battery = $item->reference->ebikeReference?->battery ?? null;
                        $color = $item->reference->bikeReference?->color->label_couleur ?? null;
                        $name = $article->nom_article ?? "Article";
                        $priceHT = $item->prix_unit_ligne / 1.2;
                        $totalHT = $priceHT * $item->quantite_ligne;
                        $subtotalHT += $totalHT;
                    @endphp

                    <tr>
                        <td>
                            {{ $name }}
                            @if ($item->size)
                                <br />
                                <small style="color: #6b7280">Taille : {{ $item->size->nom_taille ?? $item->size->libelle_taille }}</small>
                            @endif

                            @if ($color)
                                <br />
                                <small style="color: #6b7280">Couleur : {{ $color }}</small>
                            @endif

                            @if ($battery)
                                <br />
                                <small style="color: #6b7280">Batterie : {{ $battery->label_batterie }}</small>
                            @endif
                        </td>
                        <td class="text-right">{{ $item->quantite_ligne }}</td>
                        <td class="text-right">{{ number_format($priceHT, 2, ",", " ") }} €</td>
                        <td class="text-right">{{ number_format($totalHT, 2, ",", " ") }} €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $tva = $subtotalHT * 0.2;
            $subtotalTTC = $subtotalHT + $tva;
            $discount = $order->pourcentage_remise ? ($subtotalTTC * $order->pourcentage_remise) / 100 : 0;
            $shipping = $order->frais_livraison;
            $total = $subtotalTTC - $discount + $shipping;
        @endphp

        <table class="totals">
            <tr>
                <td>Sous-total HT</td>
                <td class="text-right">{{ number_format($subtotalHT, 2, ",", " ") }} €</td>
            </tr>
            <tr>
                <td>TVA (20%)</td>
                <td class="text-right">{{ number_format($tva, 2, ",", " ") }} €</td>
            </tr>
            @if ($discount > 0)
                <tr class="discount">
                    <td>Remise (-{{ $order->pourcentage_remise }}%)</td>
                    <td class="text-right">-{{ number_format($discount, 2, ",", " ") }} €</td>
                </tr>
            @endif

            <tr>
                <td>Livraison</td>
                <td class="text-right">
                    @if ($shipping > 0)
                        {{ number_format($shipping, 2, ",", " ") }} €
                    @else
                        Offerts
                    @endif
                </td>
            </tr>
            <tr class="total-row">
                <td style="padding-top: 12px">Total TTC</td>
                <td class="text-right" style="padding-top: 12px">{{ number_format($total, 2, ",", " ") }} €</td>
            </tr>
        </table>

        <div class="footer">
            <p>Cube France - SIRET : 123 456 789 00012 - TVA : FR12345678901</p>
            <p>Merci pour votre confiance !</p>
        </div>
    </body>
</html>
