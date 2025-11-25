<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cube France</title>
</head>
<body>
    <nav>
        <ul>
            @foreach ($categories as $categorie)
                <x-category-item :categorie="$categorie" />
            @endforeach
        </ul>
    </nav>
    <h1>Cube France</h1>
</body>
</html>