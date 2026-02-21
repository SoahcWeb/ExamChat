<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF token pour Inertia et Axios -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title inertia>{{ config('app.name', 'Mini ChatGPT') }}</title>

    @if(app()->environment('local'))
        <!-- Développement : Vite direct -->
        @vite('resources/js/app.ts')
    @else
        <!-- Production : fichiers buildés Vite -->
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        @endphp

        @if(isset($manifest['resources/js/app.ts']))
            @foreach($manifest['resources/js/app.ts']['css'] ?? [] as $cssFile)
                <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
            @endforeach
            <script type="module" src="{{ asset('build/' . $manifest['resources/js/app.ts']['file']) }}"></script>
        @endif
    @endif

    @inertiaHead
</head>
<body class="bg-gray-100">
    @inertia
</body>
</html>
