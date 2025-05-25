<?php

namespace App\Http\Controllers\Api\Service;

use App\Domain\UseCases\Service\UpdateServiceUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateServiceController extends Controller
{
    public function __construct(
        private readonly UpdateServiceUseCase $useCase
    ) {}

    public function __invoke(Request $request, string $serviceId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string|max:500',
                'duration' => 'sometimes|required|integer|min:1',
                'price' => 'sometimes|required|numeric|min:0'
            ]);

            $service = $this->useCase->execute($serviceId, $validated);

            if (!$service) {
                return response()->json([
                    'message' => 'Service non trouvé'
                ], 404);
            }

            return response()->json([
                'message' => 'Service mis à jour avec succès',
                'data' => $service
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
