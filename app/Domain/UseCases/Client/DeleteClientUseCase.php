<?php

namespace App\Domain\UseCases\Client;

use App\Domain\Repositories\Interfaces\ClientRepositoryInterface;

class DeleteClientUseCase
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository
    ) {}

    public function execute(string $clientId): bool
    {
        $client = $this->clientRepository->findById($clientId);

        if (!$client) {
            return false;
        }

        return $this->clientRepository->delete($clientId);
    }
}
