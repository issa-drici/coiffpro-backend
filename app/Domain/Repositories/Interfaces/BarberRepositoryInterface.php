<?php

namespace App\Domain\Repositories\Interfaces;

use App\Models\Barber;
use Illuminate\Database\Eloquent\Collection;

interface BarberRepositoryInterface
{
    /**
     * Trouve un barber par son ID
     */
    public function findById(string $id): ?Barber;

    /**
     * Trouve un barber par son user_id
     */
    public function findByUserId(string $userId): ?Barber;

    /**
     * Trouve un barber par son user_id et salon_id
     */
    public function findByUserIdAndSalonId(string $userId, string $salonId): ?Barber;

    /**
     * Récupère tous les barbers d'un salon
     */
    public function findAllBySalon(string $salonId): Collection;

    /**
     * Récupère tous les barbers actifs d'un salon
     */
    public function findActiveBySalon(string $salonId): Collection;

    /**
     * Récupère tous les barbers inactifs d'un salon
     */
    public function findInactiveBySalon(string $salonId): Collection;

    /**
     * Crée un nouveau barber
     */
    public function create(array $data): Barber;

    /**
     * Met à jour un barber
     */
    public function update(string $id, array $data): Barber;

    /**
     * Supprime un barber
     */
    public function delete(string $id): bool;

    /**
     * Active un barber
     */
    public function activate(string $id): Barber;

    /**
     * Désactive un barber
     */
    public function deactivate(string $id): Barber;
}
