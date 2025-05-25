<?php

namespace App\Http\Controllers\Api\Service;

use App\Domain\UseCases\Service\DeleteServiceUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DeleteServiceController extends Controller
{
    public function __construct(
        private readonly DeleteServiceUseCase $useCase
    ) {}

    public function __invoke(string $serviceId): JsonResponse
    {
        try {
            $deleted = $this->useCase->execute($serviceId);

            if (!$deleted) {
                return response()->json([
                    'message' => 'Service non trouvé'
                ], 404);
            }

            return response()->json([
                'message' => 'Service supprimé avec succès'
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
