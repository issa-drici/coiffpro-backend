<?php

namespace App\Domain\UseCases\Service;

use App\Domain\Repositories\Interfaces\ServiceRepositoryInterface;

class DeleteServiceUseCase
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepository
    ) {}

    public function execute(string $serviceId): bool
    {
        $service = $this->serviceRepository->findById($serviceId);

        if (!$service) {
            return false;
        }

        // Vérifier si le service est utilisé dans des files d'attente
        $queueClients = $this->serviceRepository->findByQueueClient($serviceId);
        if ($queueClients->isNotEmpty()) {
            throw new \DomainException('Impossible de supprimer ce service car il est utilisé dans des files d\'attente.');
        }

        return $this->serviceRepository->delete($serviceId);
    }
}
