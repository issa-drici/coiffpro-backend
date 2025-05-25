<?php

namespace App\Http\Controllers\Api\Client;

use App\Domain\UseCases\Client\DeleteClientUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DeleteClientController extends Controller
{
    public function __construct(
        private readonly DeleteClientUseCase $useCase
    ) {}

    public function __invoke(string $clientId): JsonResponse
    {
        try {
            $deleted = $this->useCase->execute($clientId);

            if (!$deleted) {
                return response()->json([
                    'message' => 'Client non trouvé'
                ], 404);
            }

            return response()->json([
                'message' => 'Client supprimé avec succès'
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
