<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\Interfaces\ServiceRepositoryInterface;
use App\Infrastructure\Models\ServiceModel;
use Illuminate\Database\Eloquent\Collection;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function __construct(
        private readonly ServiceModel $model
    ) {}

    public function findAll(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    public function findById(string $id): ?ServiceModel
    {
        return $this->model->find($id);
    }

    public function findAllBySalon(string $salonId): Collection
    {
        return $this->model
            ->where('salon_id', $salonId)
            ->orderBy('name')
            ->get();
    }

    public function findAllByCategory(string $category, string $salonId): Collection
    {
        return $this->model
            ->where('category', $category)
            ->where('salon_id', $salonId)
            ->orderBy('name')
            ->get();
    }

    public function create(array $data): ServiceModel
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): ServiceModel
    {
        $service = $this->findById($id);
        $service->update($data);
        return $service->fresh();
    }

    public function delete(string $id): bool
    {
        return $this->model->destroy($id) > 0;
    }

    public function findByQueueClient(string $queueClientId): Collection
    {
        return $this->model
            ->whereHas('queueClients', function ($query) use ($queueClientId) {
                $query->where('queue_clients.id', $queueClientId);
            })
            ->get();
    }
}
