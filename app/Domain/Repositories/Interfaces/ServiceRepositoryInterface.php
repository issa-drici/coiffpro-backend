<?php

namespace App\Domain\Repositories\Interfaces;

use App\Infrastructure\Models\ServiceModel;
use Illuminate\Database\Eloquent\Collection;

interface ServiceRepositoryInterface
{
    /**
     * Récupère tous les services
     */
    public function findAll(): Collection;

    /**
     * Trouve un service par son ID
     */
    public function findById(string $id): ?ServiceModel;

    /**
     * Récupère tous les services d'un salon
     */
    public function findAllBySalon(string $salonId): Collection;

    /**
     * Récupère tous les services d'une catégorie pour un salon
     */
    public function findAllByCategory(string $category, string $salonId): Collection;

    /**
     * Crée un nouveau service
     */
    public function create(array $data): ServiceModel;

    /**
     * Met à jour un service existant
     */
    public function update(string $id, array $data): ServiceModel;

    /**
     * Supprime un service
     */
    public function delete(string $id): bool;

    /**
     * Récupère les services associés à un client en file d'attente
     */
    public function findByQueueClient(string $queueClientId): Collection;
}
