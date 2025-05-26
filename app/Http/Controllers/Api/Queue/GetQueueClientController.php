<?php

namespace App\Http\Controllers\Api\Queue;

use App\Domain\UseCases\Queue\GetQueueClientUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetQueueClientController extends Controller
{
    public function __construct(
        private readonly GetQueueClientUseCase $useCase
    ) {}

    public function __invoke(string $queueClientId): JsonResponse
    {
        try {
            $queueClient = $this->useCase->execute($queueClientId);

            if (!$queueClient) {
                return response()->json([
                    'message' => 'Client non trouvé dans la file d\'attente'
                ], 404);
            }

            return response()->json([
                'data' => $queueClient
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
