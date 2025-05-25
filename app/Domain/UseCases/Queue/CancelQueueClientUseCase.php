<?php

namespace App\Domain\UseCases\Queue;

use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;

class CancelQueueClientUseCase
{
    public function __construct(
        private readonly QueueClientRepositoryInterface $queueClientRepository
    ) {}

    public function execute(string $queueClientId): bool
    {
        $queueClient = $this->queueClientRepository->findById($queueClientId);

        if (!$queueClient) {
            throw new \DomainException('Client non trouvé dans la file d\'attente.');
        }

        // Vérifier que le client n'est pas déjà terminé ou annulé
        if (in_array($queueClient->status, ['completed', 'cancelled'])) {
            throw new \DomainException('Ce client a déjà été traité.');
        }

        // Marquer le client comme annulé
        $this->queueClientRepository->update($queueClientId, [
            'status' => 'cancelled'
        ]);

        return true;
    }
}
