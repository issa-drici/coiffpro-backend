<?php

namespace App\Domain\UseCases\Client;

use App\Domain\Repositories\Interfaces\ClientRepositoryInterface;
use App\Infrastructure\Models\ClientModel;
use Illuminate\Support\Str;

class CreateClientUseCase
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository
    ) {}

    public function execute(array $data): ClientModel
    {
        $clientData = [
            'id' => (string) Str::uuid(),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'notes' => $data['notes'] ?? null
        ];

        return $this->clientRepository->create($clientData);
    }
}
