<?php

namespace App\Domain\Repositories\Interfaces;

use App\Domain\Entities\Salon;
use App\Application\DTOs\SalonWithOwnerDTO;
use App\Infrastructure\Models\SalonModel;
use Illuminate\Database\Eloquent\Collection;

interface SalonRepositoryInterface
{
    /**
     * Trouve un salon par son ID
     */
    public function findById(string $id): ?SalonModel;

    /**
     * Récupère tous les salons
     */
    public function findAll(): Collection;

    /**
     * Crée un nouveau salon
     */
    public function create(Salon $salon): Salon;

    /**
     * Met à jour un salon
     */
    public function update(Salon $salon): Salon;

    /**
     * Trouve un salon par son propriétaire
     */
    public function findByOwnerId(string $ownerId): ?Salon;

    /**
     * Trouve un salon avec les informations de son propriétaire
     */
    public function findByIdWithOwner(string $id): ?SalonWithOwnerDTO;

    /**
     * Récupère tous les salons avec les informations de leurs propriétaires
     */
    public function findAllWithOwners(): array;

    /**
     * Récupère tous les salons avec les informations de leurs propriétaires (paginé)
     */
    public function findAllWithOwnersPaginated(array $filters = [], int $page = 1, int $perPage = 10): array;

    /**
     * Trouve un salon par ses slugs
     */
    public function findBySlug(string $typeSlug, string $citySlug, string $nameSlug): ?Salon;
}
