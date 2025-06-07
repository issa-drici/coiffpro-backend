<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use App\Infrastructure\Models\QueueClientModel;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class QueueClientRepository implements QueueClientRepositoryInterface
{
    public function __construct(
        private readonly QueueClientModel $model
    ) {}

    public function findById(string $id): ?QueueClientModel
    {
        return $this->model->with(['client', 'services'])->find($id);
    }

    public function findAllBySalon(string $salonId): Collection
    {
        return $this->model
            ->with(['client', 'services'])
            ->where('salon_id', $salonId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findAllByStatus(string $status, string $salonId): Collection
    {
        return $this->model
            ->with(['client', 'services'])
            ->where('status', $status)
            ->where('salon_id', $salonId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findAllByClient(string $clientId): Collection
    {
        return $this->model
            ->with(['services'])
            ->where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data): QueueClientModel
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): QueueClientModel
    {
        $queueClient = $this->findById($id);
        $queueClient->update($data);
        return $queueClient->fresh(['client', 'services']);
    }

    public function delete(string $id): bool
    {
        return $this->model->destroy($id) > 0;
    }

    public function attachServices(string $queueClientId, array $serviceIds): void
    {
        $queueClient = $this->findById($queueClientId);
        $queueClient->services()->attach($serviceIds);
    }

    public function detachServices(string $queueClientId, array $serviceIds): void
    {
        $queueClient = $this->findById($queueClientId);
        $queueClient->services()->detach($serviceIds);
    }

    public function findNextWaiting(string $salonId): ?QueueClientModel
    {
        return $this->model
            ->with(['client', 'services'])
            ->where('salon_id', $salonId)
            ->where('status', 'waiting')
            ->orderBy('created_at', 'asc')
            ->first();
    }

    public function findCurrentInProgress(string $salonId): ?QueueClientModel
    {
        return $this->model
            ->with(['client', 'services'])
            ->where('salon_id', $salonId)
            ->where('status', 'in_progress')
            ->first();
    }

    public function findLastTicketOfDay(Carbon $date, string $salonId): ?QueueClientModel
    {
        return $this->model
            ->whereDate('created_at', $date)
            ->where('salon_id', $salonId)
            ->orderBy('ticket_number', 'desc')
            ->first();
    }

    /**
     * Récupère tous les clients en file d'attente d'un salon pour une date donnée
     */
    public function findAllBySalonAndDate(string $salonId, Carbon $date): Collection
    {
        return QueueClientModel::with(['client', 'services'])
            ->where('salon_id', $salonId)
            ->whereDate('created_at', $date)
            ->get();
    }

    /**
     * Récupère l'historique des clients en file d'attente d'un salon pour une période donnée
     */
    public function findHistoryBySalon(string $salonId, Carbon $startDate, Carbon $endDate, ?string $status = null): Collection
    {
        $query = QueueClientModel::with(['client', 'services'])
            ->where('salon_id', $salonId)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    /**
     * Met à jour le statut d'un client en file d'attente
     */
    public function updateStatus(string $id, string $status): void
    {
        QueueClientModel::where('id', $id)->update([
            'status' => $status,
            'updated_at' => Carbon::now()
        ]);
    }
}
