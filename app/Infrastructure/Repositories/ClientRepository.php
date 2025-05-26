<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\Interfaces\ClientRepositoryInterface;
use App\Infrastructure\Models\ClientModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ClientRepository implements ClientRepositoryInterface
{
    public function __construct(
        private readonly ClientModel $model
    ) {}

    public function findById(string $id): ?ClientModel
    {
        return $this->model->find($id);
    }

    public function findByPhoneAndFirstName(string $phoneNumber, string $firstName, string $salonId): ?ClientModel
    {
        return $this->model
            ->where('phoneNumber', $phoneNumber)
            ->whereRaw('LOWER(clients."firstName") = ?', [strtolower($firstName)])
            ->where('salon_id', $salonId)
            ->first();
    }

    public function findByPhoneNumber(string $phoneNumber, string $salonId): ?ClientModel
    {
        return $this->model
            ->where('phoneNumber', $phoneNumber)
            ->where('salon_id', $salonId)
            ->first();
    }

    public function findByEmail(string $email, string $salonId): ?ClientModel
    {
        return $this->model
            ->where('email', $email)
            ->where('salon_id', $salonId)
            ->first();
    }

    public function findAllBySalon(string $salonId): Collection
    {
        return $this->model
            ->where('salon_id', $salonId)
            ->orderBy('firstName')
            ->orderBy('lastName')
            ->get();
    }

    public function create(array $data): ClientModel
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): ClientModel
    {
        $client = $this->findById($id);
        $client->update($data);
        return $client->fresh();
    }

    public function delete(string $id): bool
    {
        return $this->model->destroy($id) > 0;
    }
}
