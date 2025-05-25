<?php

namespace App\Domain\UseCases\Client;

use App\Domain\Repositories\Interfaces\ClientRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GetSalonClientsUseCase
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository
    ) {}

    public function execute(string $salonId): Collection
    {
        return $this->clientRepository->findAllBySalon($salonId);
    }
}
