<?php

namespace App\Http\Controllers\Api\Queue;

use App\Domain\UseCases\Queue\GetCurrentClientUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class GetCurrentClientController extends Controller
{
    public function __construct(
        private readonly GetCurrentClientUseCase $useCase
    ) {}

    public function __invoke(string $salonId): JsonResponse
    {
        try {
            $user = Auth::user();
            $barber = $user->barber;
            if (!$barber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun barber associé à cet utilisateur.'
                ], 403);
            }
            $result = $this->useCase->execute($salonId, $barber->id);
            return response()->json($result);
        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération du client en cours.',
                'debug' => [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
}
