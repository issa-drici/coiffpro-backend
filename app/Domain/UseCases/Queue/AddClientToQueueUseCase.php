<?php

namespace App\Domain\UseCases\Queue;

use App\Domain\Repositories\Interfaces\ClientRepositoryInterface;
use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use App\Domain\Repositories\Interfaces\ServiceRepositoryInterface;
use App\Infrastructure\Models\QueueClientModel;

class AddClientToQueueUseCase
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly QueueClientRepositoryInterface $queueClientRepository,
        private readonly ServiceRepositoryInterface $serviceRepository
    ) {}

    public function execute(array $data): QueueClientModel
    {
        // Validation des données requises
        if (!isset($data['client_id']) || !isset($data['salon_id']) || !isset($data['services'])) {
            throw new \DomainException('Les champs client_id, salon_id et services sont obligatoires.');
        }

        // Vérifier que le client existe
        $client = $this->clientRepository->findById($data['client_id']);
        if (!$client) {
            throw new \DomainException('Client non trouvé.');
        }

        // Vérifier que le client appartient bien au salon
        if ($client->salon_id !== $data['salon_id']) {
            throw new \DomainException('Le client n\'appartient pas à ce salon.');
        }

        // Vérifier que tous les services existent et appartiennent au salon
        foreach ($data['services'] as $serviceId) {
            $service = $this->serviceRepository->findById($serviceId);
            if (!$service) {
                throw new \DomainException("Le service avec l'ID $serviceId n'existe pas.");
            }
            if ($service->salon_id !== $data['salon_id']) {
                throw new \DomainException("Le service avec l'ID $serviceId n'appartient pas à ce salon.");
            }
        }

        // Vérifier si le client est déjà dans la file d'attente
        $existingQueueClients = $this->queueClientRepository->findAllByClient($data['client_id']);
        foreach ($existingQueueClients as $queueClient) {
            if (in_array($queueClient->status, ['waiting', 'in_progress'])) {
                throw new \DomainException('Le client est déjà dans la file d\'attente.');
            }
        }

        // Créer l'entrée dans la file d'attente
        $queueClient = $this->queueClientRepository->create([
            'client_id' => $data['client_id'],
            'salon_id' => $data['salon_id'],
            'status' => 'waiting',
            'notes' => $data['notes'] ?? null
        ]);

        // Attacher les services
        $this->queueClientRepository->attachServices($queueClient->id, $data['services']);

        return $this->queueClientRepository->findById($queueClient->id);
    }
}
