<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Liste des vélos</h1>
    <ul>
        @foreach ($articles as $article)
            <li>{{ $article->id_article }} : {{ $article->nom_article }} // {{ $article->categorie->nom_categorie }}</li>
        @endforeach
    </ul>
    {{ $articles->links() }}
</body>
</html>