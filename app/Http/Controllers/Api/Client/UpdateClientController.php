<?php

namespace App\Http\Controllers\Api\Client;

use App\Domain\UseCases\Client\UpdateClientUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateClientController extends Controller
{
    public function __construct(
        private readonly UpdateClientUseCase $useCase
    ) {}

    public function __invoke(Request $request, string $clientId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'first_name' => 'sometimes|required|string|max:255',
                'last_name' => 'sometimes|required|string|max:255',
                'phone' => 'sometimes|required|string|max:20',
                'email' => 'nullable|email|max:255',
                'notes' => 'nullable|string|max:500'
            ]);

            $client = $this->useCase->execute($clientId, $validated);

            if (!$client) {
                return response()->json([
                    'message' => 'Client non trouvé'
                ], 404);
            }

            return response()->json([
                'message' => 'Client mis à jour avec succès',
                'data' => $client
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
