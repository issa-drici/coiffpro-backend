<?php

namespace App\Http\Controllers\Api;

use App\Domain\UseCases\Queue\AddClientToQueueUseCase;
use App\Domain\UseCases\Queue\GetWaitingClientsUseCase;
use App\Domain\UseCases\Queue\GetCurrentClientUseCase;
use App\Domain\UseCases\Queue\MoveToNextClientUseCase;
use App\Domain\UseCases\Queue\CancelQueueClientUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function __construct(
        private readonly AddClientToQueueUseCase $addClientToQueueUseCase,
        private readonly GetWaitingClientsUseCase $getWaitingClientsUseCase,
        private readonly GetCurrentClientUseCase $getCurrentClientUseCase,
        private readonly MoveToNextClientUseCase $moveToNextClientUseCase,
        private readonly CancelQueueClientUseCase $cancelQueueClientUseCase
    ) {}

    /**
     * Ajouter un client à la file d'attente
     */
    public function addClient(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'client_id' => 'required|string|uuid',
                'salon_id' => 'required|string|uuid',
                'services' => 'required|array',
                'services.*' => 'required|string|uuid',
                'notes' => 'nullable|string|max:500'
            ]);

            $queueClient = $this->addClientToQueueUseCase->execute($validated);

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

    /**
     * Récupérer les clients en attente
     */
    public function getWaitingClients(string $salonId): JsonResponse
    {
        try {
            $clients = $this->getWaitingClientsUseCase->execute($salonId);

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
     * Récupérer le client en cours de service
     */
    public function getCurrentClient(string $salonId): JsonResponse
    {
        try {
            $client = $this->getCurrentClientUseCase->execute($salonId);

            if (!$client) {
                return response()->json([
                    'message' => 'Aucun client en cours de service'
                ], 404);
            }

            return response()->json([
                'data' => $client
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Passer au client suivant
     */
    public function moveToNextClient(string $salonId): JsonResponse
    {
        try {
            $nextClient = $this->moveToNextClientUseCase->execute($salonId);

            if (!$nextClient) {
                return response()->json([
                    'message' => 'Aucun client en attente'
                ], 404);
            }

            return response()->json([
                'message' => 'Passage au client suivant effectué avec succès',
                'data' => $nextClient
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Annuler un client de la file d'attente
     */
    public function cancelClient(string $queueClientId): JsonResponse
    {
        try {
            $this->cancelQueueClientUseCase->execute($queueClientId);

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
