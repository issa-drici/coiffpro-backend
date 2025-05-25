<?php

namespace App\Http\Controllers\Api\Queue;

use App\Domain\UseCases\Queue\CancelQueueClientUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CancelQueueClientController extends Controller
{
    public function __construct(
        private readonly CancelQueueClientUseCase $useCase
    ) {}

    public function __invoke(string $queueClientId): JsonResponse
    {
        try {
            $this->useCase->execute($queueClientId);

            return response()->json([
                'message' => 'Client annulé avec succès'
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
