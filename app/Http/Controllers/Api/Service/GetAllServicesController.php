<?php

namespace App\Http\Controllers\Api\Service;

use App\Domain\UseCases\Service\GetAllServicesUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetAllServicesController extends Controller
{
    public function __construct(
        private readonly GetAllServicesUseCase $useCase
    ) {}

    public function __invoke(): JsonResponse
    {
        try {
            $services = $this->useCase->execute();

            return response()->json([
                'data' => $services
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
