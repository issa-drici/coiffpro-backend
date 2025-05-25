<?php

namespace App\Http\Controllers\Api\Service;

use App\Domain\UseCases\Service\CreateServiceUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreateServiceController extends Controller
{
    public function __construct(
        private readonly CreateServiceUseCase $useCase
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'duration' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0'
            ]);

            // Récupérer le salon_id de l'utilisateur connecté
            $user = $request->user();
            if (!$user->salon_id) {
                return response()->json([
                    'message' => 'Vous devez être associé à un salon pour créer un service'
                ], 403);
            }

            $validated['salon_id'] = $user->salon_id;

            $service = $this->useCase->execute($validated);

            return response()->json([
                'message' => 'Service créé avec succès',
                'data' => $service
            ], 201);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
