<?php

namespace App\Http\Controllers\Api\Queue;

use App\Domain\UseCases\Queue\AddClientToQueueUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddClientToQueueController extends Controller
{
    public function __construct(
        private readonly AddClientToQueueUseCase $useCase
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'client_id' => 'required|string|uuid',
                'salon_id' => 'required|string|uuid',
                'services' => 'required|array',
                'services.*' => 'required|string|uuid',
                'notes' => 'nullable|string|max:500'
            ]);

            $queueClient = $this->useCase->execute($validated);

            return response()->json([
                'message' => 'Client ajouté à la file d\'attente avec succès',
                'data' => $queueClient
            ], 201);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
