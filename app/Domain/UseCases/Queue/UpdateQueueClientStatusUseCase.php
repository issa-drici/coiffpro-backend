<?php

namespace App\Domain\UseCases\Queue;

use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use Carbon\Carbon;

class UpdateQueueClientStatusUseCase
{
    public function __construct(
        private readonly QueueClientRepositoryInterface $queueClientRepository
    ) {}

    public function execute(string $queueClientId, string $newStatus, ?string $notes = null): array
    {
        // Validation du statut
        $validStatuses = ['waiting', 'in_progress', 'completed', 'cancelled'];
        if (!in_array($newStatus, $validStatuses)) {
            throw new \DomainException('Statut invalide. Les statuts valides sont : waiting, in_progress, completed, cancelled.');
        }

        // Récupérer le client en file d'attente
        $queueClient = $this->queueClientRepository->findById($queueClientId);
        if (!$queueClient) {
            throw new \DomainException("Le client en file d'attente avec l'ID $queueClientId n'existe pas.");
        }

        // Vérifier si le changement de statut est valide
        $this->validateStatusTransition($queueClient->status, $newStatus);

        // Préparer les données de mise à jour
        $updateData = [
            'status' => $newStatus,
            'updated_at' => Carbon::now()
        ];

        // Ajouter les notes si fournies
        if ($notes !== null) {
            $updateData['notes'] = $notes;
        }

        // Si le statut passe à "completed", ajouter la date de complétion
        if ($newStatus === 'completed') {
            $updateData['completed_at'] = Carbon::now();
        }

        // Mettre à jour le statut
        $updatedQueueClient = $this->queueClientRepository->update($queueClientId, $updateData);

        return [
            'success' => true,
            'message' => 'Statut du client mis à jour avec succès',
            'data' => $updatedQueueClient
        ];
    }

    private function validateStatusTransition(string $currentStatus, string $newStatus): void
    {
        $validTransitions = [
            'waiting' => ['in_progress', 'cancelled'],
            'in_progress' => ['completed', 'cancelled'],
            'completed' => [], // Une fois complété, on ne peut plus changer le statut
            'cancelled' => [] // Une fois annulé, on ne peut plus changer le statut
        ];

        if (!in_array($newStatus, $validTransitions[$currentStatus])) {
            throw new \DomainException(
                "Transition de statut invalide. Impossible de passer de '$currentStatus' à '$newStatus'. " .
                "Les transitions valides depuis '$currentStatus' sont : " .
                implode(', ', $validTransitions[$currentStatus])
            );
        }
    }
}
