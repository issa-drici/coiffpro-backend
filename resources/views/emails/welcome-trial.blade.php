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
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bonjour {{ $userName }} !</h1>

        <p>Merci de rejoindre EatsUp et de nous faire confiance pour simplifier l'expérience de vos clients. 🎉</p>

        <p>Avec notre solution, vous allez pouvoir digitaliser vos menus, augmenter vos ventes et offrir un accès pratique à vos plats grâce aux QR codes.</p>

        <h2>Votre accès Premium gratuit</h2>
        <p>Pour bien démarrer, vous bénéficiez d'un accès Premium gratuit pendant 30 jours !</p>

        <ul>
            <li>Date de début : {{ $trialStartDate }}</li>
            <li>Date de fin : {{ $trialEndDate }}</li>
        </ul>

        <p>Pendant cette période, profitez de tous les avantages Premium :</p>
        <ul>
            <li>Scan illimités par mois</li>
            <li>Nombre de tables illimité</li>
            <li>Catégories illimitées</li>
            <li>Plats illimités (boissons, entrées, plats, desserts…)</li>
            <li>Traduction en anglais et autres langues</li>
            <li>Site web offert</li>
            <li>Et bien plus encore !</li>
        </ul>

        <p>Pour commencer, connectez-vous à votre espace sur <a href="https://www.eatsup.fr/login">https://www.eatsup.fr/login</a>. Vous y trouverez toutes les options pour personnaliser votre menu et gérer vos informations.</p>

        <p>Nous sommes là pour vous accompagner, alors n'hésitez pas à nous contacter si vous avez des questions.</p>

        <p style="margin: 30px 0;">
            <a href="{{ $upgradeUrl }}" class="button">Découvrir tous les avantages Premium</a>
        </p>

        <p>Bienvenue dans la communauté EatsUp !</p>
        <p>Cordialement,<br>L'équipe EatsUp</p>
        <img src="https://www.eatsup.fr/_next/image?url=%2Fimages%2Flogo.png&w=1200&q=75" alt="EatsUp Logo" class="logo">

    </div>
</body>
</html>
