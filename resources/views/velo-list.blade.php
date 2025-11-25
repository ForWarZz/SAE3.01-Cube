<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Liste des vélos</h1>
    <ul>
        @foreach ($velos as $velo)
            <li>{{ $velo->id_article }} : {{ $velo->nom_article }} // {{ $velo->article->categorie->nom_categorie }}</li>
        @endforeach
    </ul>
    {{ $velos->links() }}
</body>
</html>