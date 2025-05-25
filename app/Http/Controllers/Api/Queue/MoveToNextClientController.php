<?php

namespace App\Http\Controllers\Api\Queue;

use App\Domain\UseCases\Queue\MoveToNextClientUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class MoveToNextClientController extends Controller
{
    public function __construct(
        private readonly MoveToNextClientUseCase $useCase
    ) {}

    public function __invoke(string $salonId): JsonResponse
    {
        try {
            $nextClient = $this->useCase->execute($salonId);

            if (!$nextClient) {
                return response()->json([
                    'message' => 'Aucun client en attente'
                ], 404);
            }

            return response()->json([
                'message' => 'Passage au client suivant effectué avec succès',
                'data' => $nextClient
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
