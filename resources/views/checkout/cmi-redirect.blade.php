<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Redirection vers le paiement sécurisé...</title>
</head>
<body>
    <p style="font-family: sans-serif; text-align: center; margin-top: 4rem;">Redirection vers la plateforme de paiement sécurisée CMI...</p>
    <form id="cmi-form" action="{{ $gatewayUrl }}" method="POST">
        @foreach ($fields as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>
    <script>document.getElementById('cmi-form').submit();</script>
</body>
</html>
