<?php

namespace App\Http\Controllers\Api\Queue;

use App\Domain\UseCases\Queue\UpdateQueueClientStatusUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpdateQueueClientStatusController extends Controller
{
    public function __construct(
        private readonly UpdateQueueClientStatusUseCase $updateQueueClientStatusUseCase
    ) {}

    public function __invoke(Request $request, string $queueClientId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|string|in:waiting,in_progress,completed,cancelled',
                'notes' => 'nullable|string'
            ]);

            $result = $this->updateQueueClientStatusUseCase->execute(
                $queueClientId,
                $validated['status'],
                $validated['notes'] ?? null
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
                'message' => 'Une erreur est survenue lors de la mise à jour du statut.'
            ], 500);
        }
    }
}
