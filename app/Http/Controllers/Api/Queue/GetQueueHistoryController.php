<?php

namespace App\Http\Controllers\Api\Queue;

use App\Domain\UseCases\Queue\GetQueueHistoryUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetQueueHistoryController extends Controller
{
    public function __construct(
        private readonly GetQueueHistoryUseCase $useCase
    ) {}

    public function __invoke(Request $request, string $salonId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => 'nullable|date|date_format:Y-m-d',
                'end_date' => 'nullable|date|date_format:Y-m-d',
                'status' => 'nullable|string|in:waiting,in_progress,completed,cancelled'
            ]);

            $result = $this->useCase->execute(
                $salonId,
                $validated['start_date'] ?? null,
                $validated['end_date'] ?? null,
                $validated['status'] ?? null
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
                'message' => 'Une erreur est survenue lors de la récupération de l\'historique.'
            ], 500);
        }
    }
}
