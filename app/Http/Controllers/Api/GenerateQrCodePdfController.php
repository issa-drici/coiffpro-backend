<?php

namespace App\Http\Controllers\Api;

use App\Domain\UseCases\GenerateQrCodePdfUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GenerateQrCodePdfController extends Controller
{
    public function __construct(
        private readonly GenerateQrCodePdfUseCase $useCase
    ) {}

    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        // Transmettre l'utilisateur connecté au cas d'usage
        return $this->useCase->execute($user);
    }
}
