<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mini ChatGPT - Test</title>
</head>
<body>
    <h1>Mini ChatGPT - Test</h1>

    <form method="POST" action="{{ route('ask.test') }}">
        @csrf
        <label for="question">Pose ta question :</label>
        <input type="text" id="question" name="question" required>
        <button type="submit">Envoyer</button>
    </form>

    @if(isset($responseText))
        <h2>Réponse :</h2>
        <p>{{ $responseText }}</p>
    @endif
</body>
</html>
