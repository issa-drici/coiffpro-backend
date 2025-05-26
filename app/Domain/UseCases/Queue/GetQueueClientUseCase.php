<?php

namespace App\Domain\UseCases\Queue;

use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use App\Infrastructure\Models\QueueClientModel;
use Carbon\Carbon;

class GetQueueClientUseCase
{
    public function __construct(
        private readonly QueueClientRepositoryInterface $queueClientRepository
    ) {}

    public function execute(string $queueClientId): ?QueueClientModel
    {
        $queueClient = $this->queueClientRepository->findById($queueClientId);

        if (!$queueClient) {
            return null;
        }

        // Récupérer tous les clients en attente et en cours pour ce salon
        $currentClient = $this->queueClientRepository->findCurrentInProgress($queueClient->salon_id);
        $waitingClients = $this->queueClientRepository->findAllByStatus('waiting', $queueClient->salon_id);

        // Calculer la durée totale
        $totalDuration = 0;

        // Ajouter la durée du client en cours si présent
        if ($currentClient) {
            $totalDuration += $currentClient->services->sum('duration');
        }

        // Ajouter la durée des clients en attente jusqu'à notre client
        foreach ($waitingClients as $client) {
            if ($client->id === $queueClientId) {
                break;
            }
            $totalDuration += $client->services->sum('duration');
        }

        // Calculer l'heure estimée en ajoutant la durée totale à l'heure actuelle
        $estimatedTime = Carbon::now()->addMinutes($totalDuration);

        // Ajouter le temps estimé à l'objet queueClient
        $queueClient->estimatedTime = $estimatedTime->toIso8601String();

        return $queueClient;
    }
}
