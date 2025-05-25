<?php

namespace App\Http\Controllers\Api;

use App\Domain\UseCases\Client\CreateClientUseCase;
use App\Domain\UseCases\Client\GetSalonClientsUseCase;
use App\Domain\UseCases\Client\UpdateClientUseCase;
use App\Domain\UseCases\Client\DeleteClientUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(
        private readonly CreateClientUseCase $createClientUseCase,
        private readonly GetSalonClientsUseCase $getSalonClientsUseCase,
        private readonly UpdateClientUseCase $updateClientUseCase,
        private readonly DeleteClientUseCase $deleteClientUseCase
    ) {}

    /**
     * Créer un nouveau client
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'salon_id' => 'required|string|uuid',
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'phoneNumber' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',
                'notes' => 'nullable|string|max:500'
            ]);

            $client = $this->createClientUseCase->execute($validated);

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

    /**
     * Récupérer tous les clients d'un salon
     */
    public function index(string $salonId): JsonResponse
    {
        try {
            $clients = $this->getSalonClientsUseCase->execute($salonId);

            return response()->json([
                'data' => $clients
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Mettre à jour un client
     */
    public function update(Request $request, string $clientId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'firstName' => 'sometimes|required|string|max:255',
                'lastName' => 'sometimes|required|string|max:255',
                'phoneNumber' => 'sometimes|required|string|max:20',
                'email' => 'nullable|email|max:255',
                'notes' => 'nullable|string|max:500'
            ]);

            $client = $this->updateClientUseCase->execute($clientId, $validated);

            return response()->json([
                'message' => 'Client mis à jour avec succès',
                'data' => $client
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Supprimer un client
     */
    public function destroy(string $clientId): JsonResponse
    {
        try {
            $this->deleteClientUseCase->execute($clientId);

            return response()->json([
                'message' => 'Client supprimé avec succès'
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
