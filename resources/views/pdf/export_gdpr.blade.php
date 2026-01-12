<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Export de données - {{ $data["client"]["nom"] }}</title>
        <style>
            body {
                font-family: sans-serif;
                font-size: 12px;
            }
            h1 {
                color: #333;
            }
            .section {
                margin-bottom: 20px;
                border-bottom: 1px solid #ccc;
                padding-bottom: 10px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }
            th,
            td {
                border: 1px solid #eee;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f8f8f8;
            }
        </style>
    </head>
    <body>
        <h1>Rapport de données personnelles (RGPD)</h1>
        <p>Généré le : {{ $data["export_date"] }}</p>

        <div class="section">
            <h2>Informations Client</h2>
            <p>
                <strong>Nom :</strong>
                {{ $data["client"]["civilite"] }} {{ $data["client"]["prenom"] }} {{ $data["client"]["nom"] }}
            </p>
            <p>
                <strong>Email :</strong>
                {{ $data["client"]["email"] }}
            </p>
            <p>
                <strong>Date de naissance :</strong>
                {{ $data["client"]["date_naissance"] }}
            </p>
        </div>

        <div class="section">
            <h2>Adresses</h2>
            @foreach ($data["adresses"] as $addr)
                <p>
                    {{ $addr["alias"] }} : {{ $addr["numero_voie"] }} {{ $addr["rue"] }}, {{ $addr["code_postal"] }}
                    {{ $addr["ville"] }}
                </p>
            @endforeach
        </div>

        <div class="section">
            <h2>Vélos enregistrés</h2>
            @if (count($data["velos_enregistres"]) > 0)
                <table>
                    <tr>
                        <th>Numéro de série</th>
                        <th>Date d'achat</th>
                        <th>Millésime</th>
                        <th>Magasin</th>
                    </tr>
                    @foreach ($data["velos_enregistres"] as $bike)
                        <tr>
                            <td>{{ $bike["numero_serie"] ?? "N/A" }}</td>
                            <td>{{ $bike["date_achat"] ?? "N/A" }}</td>
                            <td>{{ $bike["millesime"] ?? "N/A" }}</td>
                            <td>{{ $bike["magasin"] ?? "N/A" }}</td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p>Aucun vélo enregistré.</p>
            @endif
        </div>

        <div class="section">
            <h2>Historique des commandes</h2>
            @foreach ($data["commandes"] as $order)
                <div style="background: #fdfdfd; padding: 10px; margin-bottom: 10px">
                    <strong>Commande n°{{ $order["numero"] }}</strong>
                    ({{ $order["date"] }})
                    <br />
                    Mode de paiement : {{ $order["moyen_paiement"] }}
                    <table>
                        <tr>
                            <th>Produit</th>
                            <th>Qté</th>
                            <th>Prix Unitaire</th>
                        </tr>
                        @foreach ($order["articles"] as $item)
                            <tr>
                                <td>{{ $item["produit"] }}</td>
                                <td>{{ $item["quantite"] }}</td>
                                <td>{{ $item["prix_unitaire"] }} €</td>
                            </tr>
                        @endforeach
                    </table>

                    @if (count($order["demandes_retour"]) > 0)
                        <div style="margin-top: 10px; padding: 10px; background: #fff3cd; border-left: 3px solid #ffc107">
                            <strong>Demandes de retour :</strong>
                            @foreach ($order["demandes_retour"] as $returnRequest)
                                <div style="margin-top: 5px">
                                    <p>
                                        <strong>Date :</strong>
                                        {{ $returnRequest["date_demande"] }} |
                                        <strong>État :</strong>
                                        {{ $returnRequest["etat"] ?? "N/A" }}
                                    </p>
                                    @if ($returnRequest["description"])
                                        <p>
                                            <strong>Description :</strong>
                                            {{ $returnRequest["description"] }}
                                        </p>
                                    @endif

                                    @if (count($returnRequest["articles_retournes"]) > 0)
                                        <table style="margin-top: 5px">
                                            <tr>
                                                <th>Produit retourné</th>
                                                <th>Quantité</th>
                                            </tr>
                                            @foreach ($returnRequest["articles_retournes"] as $returnedItem)
                                                <tr>
                                                    <td>{{ $returnedItem["produit"] }}</td>
                                                    <td>{{ $returnedItem["quantite_retournee"] }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </body>
</html>
