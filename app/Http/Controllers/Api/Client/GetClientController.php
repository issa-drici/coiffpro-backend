<?php

namespace App\Http\Controllers\Api\Client;

use App\Domain\UseCases\Client\GetClientUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetClientController extends Controller
{
    public function __construct(
        private readonly GetClientUseCase $useCase
    ) {}

    public function __invoke(string $clientId): JsonResponse
    {
        try {
            $client = $this->useCase->execute($clientId);

            if (!$client) {
                return response()->json([
                    'message' => 'Client non trouvé'
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
