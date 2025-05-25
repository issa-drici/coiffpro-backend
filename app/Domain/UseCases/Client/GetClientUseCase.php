<?php

namespace App\Domain\UseCases\Client;

use App\Domain\Repositories\Interfaces\ClientRepositoryInterface;
use App\Infrastructure\Models\ClientModel;

class GetClientUseCase
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository
    ) {}

    public function execute(string $clientId): ?ClientModel
    {
        return $this->clientRepository->findById($clientId);
    }
}
