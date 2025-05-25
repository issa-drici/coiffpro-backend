<?php

namespace App\Http\Controllers\Api\Queue;

use App\Domain\UseCases\Queue\GetCurrentClientUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetCurrentClientController extends Controller
{
    public function __construct(
        private readonly GetCurrentClientUseCase $useCase
    ) {}

    public function __invoke(string $salonId): JsonResponse
    {
        try {
            $client = $this->useCase->execute($salonId);

            if (!$client) {
                return response()->json([
                    'message' => 'Aucun client en cours de service'
                ], 404);
            }

            return response()->json([
                'data' => $client
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
