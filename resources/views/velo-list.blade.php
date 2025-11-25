<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <ul>
        @foreach ($velos as $velo)
            <li>{{ $velo->id_article }} : {{ $velo->nom_article }}</li>
        @endforeach
    </ul>
</body>
</html>