<?php

namespace App\Domain\UseCases;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Illuminate\Http\Response;

class GenerateQrCodePdfUseCase
{
    public function execute(User $user): Response
    {
        // Récupérer le salon de l'utilisateur
        $salon = $user->salon;

        if (!$salon) {
            throw new \DomainException('Aucun salon trouvé pour cet utilisateur');
        }

        // Construire l'URL du QR code
        $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
        $queueUrl = $frontendUrl . '/salon/' . $salon->id . '/queue';

        // Générer le titre automatiquement
        $title = $salon->name;

        // Créer le QR code avec endroid/qr-code
        $qrCode = QrCode::create($queueUrl)
            ->setSize(300)
            ->setMargin(10)
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Créer le writer PNG
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Récupérer les données de l'image
        $qrCodeData = $result->getString();
        $qrCodeBase64 = base64_encode($qrCodeData);

        // Générer le PDF avec la vue Blade
        $pdf = Pdf::loadView('pdf.qrcode', [
            'title' => $title,
            'url' => $queueUrl,
            'qrCodeBase64' => $qrCodeBase64
        ]);

        $pdf->setPaper('A4', 'portrait');

        // Retourner le PDF
        return $pdf->stream('qrcode-' . $salon->name_slug . '-' . time() . '.pdf');
    }
}
