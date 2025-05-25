<?php

namespace App\Http\Controllers\Api\Service;

use App\Domain\UseCases\Service\GetServiceUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetServiceController extends Controller
{
    public function __construct(
        private readonly GetServiceUseCase $useCase
    ) {}

    public function __invoke(string $serviceId): JsonResponse
    {
        try {
            $service = $this->useCase->execute($serviceId);

            if (!$service) {
                return response()->json([
                    'message' => 'Service non trouvé'
                ], 404);
            }

            return response()->json([
                'data' => $service
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
