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
        .features {
            background-color: #f3f4f6;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .shipping-info {
            border-left: 4px solid #4F46E5;
            padding-left: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎉 Félicitations {{ $userName }} !</h1>

        <p>Votre souscription au plan Premium a été confirmée avec succès.</p>

        <div class="features">
            <h2>Vos avantages Premium :</h2>
            <ul>
                <li>Catégories de menu illimitées</li>
                <li>Photos des plats</li>
                <li>Autocollants QR codes offerts</li>
                <li>Site web inclus offert</li>
                <li>Support prioritaire</li>
            </ul>
        </div>

        <div class="shipping-info">
            <h2>🚚 Livraison de votre kit Premium</h2>
            <p>Dans les prochains jours, vous recevrez gratuitement :</p>
            <ul>
                <li>30 autocollants QR code</li>
                <li>1 affiche vitrine pour attirer les passants</li>
                <li>1 sticker pour le comptoir</li>
            </ul>
            <p><em>Délai de livraison estimé : 5-7 jours ouvrés</em></p>
        </div>

        <p>Pour commencer à profiter de toutes ces fonctionnalités :</p>

        <p style="margin: 30px 0;">
            <a href="{{ $dashboardUrl }}" class="button">Accéder à mon tableau de bord</a>
        </p>

        <p>Si vous avez des questions, notre équipe de support est là pour vous aider.</p>

        <p>Merci de votre confiance !</p>
        <p>L'équipe Eatsup</p>
    </div>
</body>
</html>
