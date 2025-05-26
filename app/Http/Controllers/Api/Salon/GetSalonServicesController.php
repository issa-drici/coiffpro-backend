<?php

namespace App\Http\Controllers\Api\Salon;

use App\Domain\UseCases\Service\GetSalonServicesUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetSalonServicesController extends Controller
{
    public function __construct(
        private readonly GetSalonServicesUseCase $useCase
    ) {}

    public function __invoke(string $salonId): JsonResponse
    {
        try {
            $services = $this->useCase->execute($salonId);

            return response()->json([
                'data' => $services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'price' => $service->price,
                        'duration' => $service->duration,
                        'created_at' => $service->created_at,
                        'updated_at' => $service->updated_at
                    ];
                })
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
