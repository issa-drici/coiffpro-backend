<?php

namespace App\Http\Controllers\Api\Queue;

use App\Domain\UseCases\Queue\CancelQueueClientUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CancelQueueClientController extends Controller
{
    public function __construct(
        private readonly CancelQueueClientUseCase $useCase
    ) {}

    public function __invoke(Request $request, string $queueClientId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'cancellation_reason' => 'nullable|string|max:500'
            ]);

            $result = $this->useCase->execute(
                $queueClientId,
                $validated['cancellation_reason'] ?? null
            );

            return response()->json($result);
        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'annulation du client.'
            ], 500);
        }
    }
}
