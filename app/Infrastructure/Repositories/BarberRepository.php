<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\Interfaces\BarberRepositoryInterface;
use App\Models\Barber;
use Illuminate\Database\Eloquent\Collection;

class BarberRepository implements BarberRepositoryInterface
{
    public function __construct(
        private readonly Barber $model
    ) {}

    public function findById(string $id): ?Barber
    {
        return $this->model->find($id);
    }

    public function findByUserId(string $userId): ?Barber
    {
        return $this->model
            ->where('user_id', $userId)
            ->first();
    }

    public function findByUserIdAndSalonId(string $userId, string $salonId): ?Barber
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('salon_id', $salonId)
            ->first();
    }

    public function findAllBySalon(string $salonId): Collection
    {
        return $this->model
            ->where('salon_id', $salonId)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findActiveBySalon(string $salonId): Collection
    {
        return $this->model
            ->where('salon_id', $salonId)
            ->where('is_active', true)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findInactiveBySalon(string $salonId): Collection
    {
        return $this->model
            ->where('salon_id', $salonId)
            ->where('is_active', false)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data): Barber
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): Barber
    {
        $barber = $this->findById($id);
        $barber->update($data);
        return $barber->fresh();
    }

    public function delete(string $id): bool
    {
        return $this->model->destroy($id) > 0;
    }

    public function activate(string $id): Barber
    {
        $barber = $this->findById($id);
        $barber->update([
            'is_active' => true,
            'is_active_changed_at' => now(),
        ]);
        return $barber->fresh();
    }

    public function deactivate(string $id): Barber
    {
        $barber = $this->findById($id);
        $barber->update([
            'is_active' => false,
            'is_active_changed_at' => now(),
        ]);
        return $barber->fresh();
    }
}
