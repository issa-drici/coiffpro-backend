<?php

namespace App\Http\Controllers\Api\Queue;

use App\Domain\UseCases\Queue\GetWaitingClientsUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetWaitingClientsController extends Controller
{
    public function __construct(
        private readonly GetWaitingClientsUseCase $useCase
    ) {}

    public function __invoke(Request $request, string $salonId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'date' => 'nullable|date|date_format:Y-m-d'
            ]);

            $result = $this->useCase->execute(
                $salonId,
                $validated['date'] ?? null
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
                'message' => 'Une erreur est survenue lors de la récupération des clients en attente.'
            ], 500);
        }
    }
}
