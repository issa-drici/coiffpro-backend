<?php

namespace App\Http\Controllers\Api\Queue;

use App\Domain\UseCases\Queue\GetWaitingClientsUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetWaitingClientsController extends Controller
{
    public function __construct(
        private readonly GetWaitingClientsUseCase $useCase
    ) {}

    public function __invoke(string $salonId): JsonResponse
    {
        try {
            $clients = $this->useCase->execute($salonId);

            return response()->json([
                'data' => $clients
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
