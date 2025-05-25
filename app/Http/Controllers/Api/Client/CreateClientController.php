<?php

namespace App\Http\Controllers\Api\Client;

use App\Domain\UseCases\Client\CreateClientUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreateClientController extends Controller
{
    public function __construct(
        private readonly CreateClientUseCase $useCase
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',
                'notes' => 'nullable|string|max:500'
            ]);

            $client = $this->useCase->execute($validated);

            return response()->json([
                'message' => 'Client créé avec succès',
                'data' => $client
            ], 201);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
