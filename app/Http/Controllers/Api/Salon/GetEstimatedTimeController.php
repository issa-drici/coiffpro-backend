<?php

namespace App\Http\Controllers\Api\Salon;

use App\Domain\UseCases\Queue\GetEstimatedTimeUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetEstimatedTimeController extends Controller
{
    public function __construct(
        private readonly GetEstimatedTimeUseCase $useCase
    ) {}

    public function __invoke(string $salonId): JsonResponse
    {
        try {
            $estimatedTime = $this->useCase->execute($salonId);

            return response()->json([
                'data' => [
                    'estimatedTime' => $estimatedTime->toIso8601String()
                ]
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
