<?php

namespace App\Domain\Repositories\Interfaces;

use App\Infrastructure\Models\ClientModel;
use Illuminate\Database\Eloquent\Collection;

interface ClientRepositoryInterface
{
    /**
     * Trouve un client par son ID
     */
    public function findById(string $id): ?ClientModel;

    /**
     * Trouve un client par son numéro de téléphone et son salon
     */
    public function findByPhoneNumber(string $phoneNumber, string $salonId): ?ClientModel;

    /**
     * Trouve un client par son email et son salon
     */
    public function findByEmail(string $email, string $salonId): ?ClientModel;

    /**
     * Récupère tous les clients d'un salon
     */
    public function findAllBySalon(string $salonId): Collection;

    /**
     * Crée un nouveau client
     */
    public function create(array $data): ClientModel;

    /**
     * Met à jour un client existant
     */
    public function update(string $id, array $data): ClientModel;

    /**
     * Supprime un client
     */
    public function delete(string $id): bool;
}
