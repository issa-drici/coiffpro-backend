<?php

namespace App\Http\Controllers\Api\Salon;

use App\Domain\UseCases\Queue\AddNewClientToQueueUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddNewClientToQueueController extends Controller
{
    public function __construct(
        private readonly AddNewClientToQueueUseCase $useCase
    ) {}

    public function __invoke(Request $request, string $salonId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phoneNumber' => 'required|string|max:20',
                'services' => 'required|array',
                'services.*' => 'required|string|uuid',
                'notes' => 'nullable|string|max:500'
            ]);

            // Ajouter le salon_id aux données validées
            $validated['salon_id'] = $salonId;

            $result = $this->useCase->execute($validated);

            return response()->json([
                'message' => $result['message'],
                'data' => $result['data']
            ], 201);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
