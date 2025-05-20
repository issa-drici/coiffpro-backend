<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Salon;
use App\Application\DTOs\SalonWithOwnerDTO;

interface SalonRepositoryInterface
{
    public function create(Salon $salon): Salon;
    public function findByOwnerId(string $ownerId): ?Salon;
    public function findByIdWithOwner(string $id): ?SalonWithOwnerDTO;
    public function findAllWithOwners(): array;
    public function findAllWithOwnersPaginated(array $filters = [], int $page = 1, int $perPage = 10): array;
    public function update(Salon $salon): Salon;
    public function findById(string $id): ?Salon;
    public function findBySlug(string $typeSlug, string $citySlug, string $nameSlug): ?Salon;
    public function findAll(): array;
}
