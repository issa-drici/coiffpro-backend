<?php

namespace App\Domain\Repositories\Interfaces;

use App\Infrastructure\Models\QueueClientModel;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

interface QueueClientRepositoryInterface
{
    /**
     * Trouve un client en file d'attente par son ID
     */
    public function findById(string $id): ?QueueClientModel;

    /**
     * Récupère tous les clients en file d'attente d'un salon
     */
    public function findAllBySalon(string $salonId): Collection;

    /**
     * Récupère tous les clients en file d'attente d'un salon avec un statut spécifique
     */
    public function findAllByStatus(string $status, string $salonId): Collection;

    /**
     * Récupère tous les clients en file d'attente d'un barber avec un statut spécifique
     */
    public function findAllByStatusAndBarber(string $status, string $salonId, string $barberId): Collection;

    /**
     * Récupère tous les clients en file d'attente d'un client
     */
    public function findAllByClient(string $clientId): Collection;

    /**
     * Crée un nouveau client en file d'attente
     */
    public function create(array $data): QueueClientModel;

    /**
     * Met à jour un client en file d'attente
     */
    public function update(string $id, array $data): QueueClientModel;

    /**
     * Supprime un client de la file d'attente
     */
    public function delete(string $id): bool;

    /**
     * Ajoute des services à un client en file d'attente
     */
    public function attachServices(string $queueClientId, array $serviceIds): void;

    /**
     * Retire des services d'un client en file d'attente
     */
    public function detachServices(string $queueClientId, array $serviceIds): void;

    /**
     * Récupère le prochain client en attente pour un salon
     */
    public function findNextWaiting(string $salonId): ?QueueClientModel;

    /**
     * Récupère le client actuellement en cours de service pour un salon et un barber
     */
    public function findCurrentInProgress(string $salonId, string $barberId): ?QueueClientModel;

    /**
     * Récupère le dernier ticket créé pour une date et un salon donnés
     */
    public function findLastTicketOfDay(Carbon $date, string $salonId): ?QueueClientModel;

    /**
     * Récupère tous les clients en file d'attente d'un salon pour une date donnée
     */
    public function findAllBySalonAndDate(string $salonId, Carbon $date): Collection;

    /**
     * Récupère l'historique des clients en file d'attente d'un salon pour une période donnée
     */
    public function findHistoryBySalon(string $salonId, Carbon $startDate, Carbon $endDate, ?string $status = null): Collection;

    /**
     * Met à jour le statut d'un client en file d'attente
     */
    public function updateStatus(string $id, string $status): void;
}
