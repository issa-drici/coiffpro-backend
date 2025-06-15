<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ strtoupper($title) }}</title>
    <style>
        html,
        body,
        * {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        html,
        body {
            width: 100vw;
            height: 100vh;
        }

        b,
        strong,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .main-title,
        .salon-name,
        .label-above,
        .label-below,
        .footer {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif !important;
            font-weight: bold !important;
        }

        body {
            min-height: 100vh;
            min-width: 100vw;
            position: relative;
            color: #fff;
        }

        .bg {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw;
            height: 100vh;
            z-index: 0;
            background: url({{ public_path('images/barber-bg.jpg') }}) center center/cover no-repeat;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(20, 20, 20, 0.75);
            z-index: 1;
        }

        .content {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            min-width: 100vw;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .main-title {
            font-size: 56px;
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 0;
            text-shadow: 0 2px 8px #000;
        }

        .salon-name {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 0;
            text-transform: uppercase;
            text-shadow: 0 2px 8px #000;
        }

        .label-above {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 0;
            text-transform: uppercase;
            text-shadow: 0 2px 8px #000;
        }

        .qr-code {
            border-radius: 18px;
            padding: 18px;
            display: inline-block;
            margin: 0;
        }

        .qr-code img {
            max-width: 500px;
            height: auto;
            display: block;
        }

        .label-below {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-top: 0;
            margin-bottom: 0;
            text-transform: uppercase;
            text-shadow: 0 2px 8px #000;
        }

        .footer {
            margin-top: 0;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-shadow: 0 2px 8px #000;
        }
    </style>
</head>

<body>
    <div class="bg"></div>
    <div class="overlay"></div>
    <div class="content">
        <div style="height: 8px"></div>

        <div style="height: 150px"></div>

        <div class="salon-name">{{ strtoupper($title) }}</div>
        <div style="height: 75px"></div>





        <!-- <div class="label-above">SCANNE ICI</div>
        <div style="height: 32px"></div> -->

        <div class="qr-code">
            <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR code">
        </div>
        <div style="height: 48px"></div>

        <div class="main-title">SCANNE POUR REJOINDRE LA FILE D'ATTENTE</div>
        <div style="height: 48px"></div>
    </div>
</body>

</html>