<?php

namespace App\Domain\UseCases\Queue;

use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use App\Infrastructure\Models\QueueClientModel;

class MoveToNextClientUseCase
{
    public function __construct(
        private readonly QueueClientRepositoryInterface $queueClientRepository
    ) {}

    public function execute(string $salonId): ?QueueClientModel
    {
        // Vérifier s'il y a un client en cours
        $currentClient = $this->queueClientRepository->findCurrentInProgress($salonId);
        if ($currentClient) {
            // Marquer le client actuel comme terminé
            $this->queueClientRepository->update($currentClient->id, [
                'status' => 'completed'
            ]);
        }

        // Récupérer le prochain client en attente
        $nextClient = $this->queueClientRepository->findNextWaiting($salonId);
        if (!$nextClient) {
            return null;
        }

        // Marquer le prochain client comme en cours
        $this->queueClientRepository->update($nextClient->id, [
            'status' => 'in_progress'
        ]);

        return $this->queueClientRepository->findById($nextClient->id);
    }
}
