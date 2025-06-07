<?php

namespace App\Domain\UseCases\Queue;

use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use Carbon\Carbon;

class CancelQueueClientUseCase
{
    public function __construct(
        private readonly QueueClientRepositoryInterface $queueClientRepository
    ) {}

    public function execute(string $queueClientId, ?string $cancellationReason = null): array
    {
        // Récupérer le client en file d'attente
        $queueClient = $this->queueClientRepository->findById($queueClientId);
        if (!$queueClient) {
            throw new \DomainException("Le client en file d'attente avec l'ID $queueClientId n'existe pas.");
        }

        // Vérifier si le client peut être annulé
        if (!in_array($queueClient->status, ['waiting', 'in_progress'])) {
            throw new \DomainException(
                "Impossible d'annuler un client avec le statut '{$queueClient->status}'. " .
                "Seuls les clients en attente ou en cours peuvent être annulés."
            );
        }

        // Préparer les données de mise à jour
        $updateData = [
            'status' => 'cancelled',
            'updated_at' => Carbon::now(),
            'cancelled_at' => Carbon::now()
        ];

        // Ajouter la raison d'annulation si fournie
        if ($cancellationReason !== null) {
            $updateData['notes'] = $queueClient->notes
                ? $queueClient->notes . "\nRaison d'annulation : " . $cancellationReason
                : "Raison d'annulation : " . $cancellationReason;
        }

        // Mettre à jour le statut
        $updatedQueueClient = $this->queueClientRepository->update($queueClientId, $updateData);

        return [
            'success' => true,
            'message' => 'Client annulé avec succès',
            'data' => $updatedQueueClient
        ];
    }
}
