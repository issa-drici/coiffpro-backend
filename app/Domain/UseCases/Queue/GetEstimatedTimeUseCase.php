<?php

namespace App\Domain\UseCases\Queue;

use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use Carbon\Carbon;

class GetEstimatedTimeUseCase
{
    public function __construct(
        private readonly QueueClientRepositoryInterface $queueClientRepository
    ) {}

    public function execute(string $salonId): Carbon
    {
        // Récupérer le client en cours de service
        $currentClient = $this->queueClientRepository->findCurrentInProgress($salonId);

        // Récupérer tous les clients en attente
        $waitingClients = $this->queueClientRepository->findAllByStatus('waiting', $salonId);

        // Calculer la durée totale
        $totalDuration = 0;

        // Ajouter la durée du client en cours si présent
        if ($currentClient) {
            $totalDuration += $currentClient->services->sum('duration');
        }

        // Ajouter la durée des clients en attente
        foreach ($waitingClients as $client) {
            $totalDuration += $client->services->sum('duration');
        }

        // Calculer l'heure estimée en ajoutant la durée totale à l'heure actuelle
        return Carbon::now()->addMinutes($totalDuration);
    }
}
