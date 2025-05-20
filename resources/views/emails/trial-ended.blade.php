<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .button {
            background-color: #4F46E5;
            color: #FFF;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Votre période d'essai Premium est terminée</h1>

        <p>Bonjour {{ $userName }},</p>

        <p>Votre période d'essai Premium est maintenant terminée. Votre compte est passé automatiquement au plan Basic.</p>

        <p>Pour retrouver l'accès aux fonctionnalités Premium :</p>
        <ul>
            <li>10 catégories de menu au lieu de 5</li>
            <li>50 articles au menu au lieu de 15</li>
            <li>5 QR codes au lieu d'1 seul</li>
        </ul>

        <p style="margin: 30px 0;">
            <a href="{{ $upgradeUrl }}" class="button">Passer au plan Premium</a>
        </p>

        <p>L'équipe EatsUp</p>
    </div>
</body>
</html>
