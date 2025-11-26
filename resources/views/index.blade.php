<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cube France</title>
    <link rel="stylesheet" href="{{ asset("navbar/style.css") }}">
</head>
<body>
    <nav>
        <ul class="ul-navbar">
            @foreach ($categories as $categorie)
                <x-category-item :categorie="$categorie" :n="0" />
            @endforeach
        </ul>
    </nav>
    <h1>Cube France</h1>

    <script src="{{ asset("navbar/main.js") }}" defer></script>
</body>
</html>