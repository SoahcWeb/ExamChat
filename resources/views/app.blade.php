<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF token pour Inertia et Axios -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title inertia>{{ config('app.name', 'Mini ChatGPT') }}</title>

    @vite('resources/js/app.ts')
    @inertiaHead
</head>
<body class="bg-gray-100">
    @inertia
</body>
</html>
