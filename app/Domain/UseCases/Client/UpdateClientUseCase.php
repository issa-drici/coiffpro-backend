<?php

namespace App\Domain\UseCases\Client;

use App\Domain\Repositories\Interfaces\ClientRepositoryInterface;
use App\Infrastructure\Models\ClientModel;

class UpdateClientUseCase
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository
    ) {}

    public function execute(string $clientId, array $data): ?ClientModel
    {
        $client = $this->clientRepository->findById($clientId);

        if (!$client) {
            return null;
        }

        $client->fill($data);
        return $this->clientRepository->update($clientId, $data);
    }
}
