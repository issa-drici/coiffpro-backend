<?php

namespace App\Domain\UseCases\Service;

use App\Domain\Repositories\Interfaces\ServiceRepositoryInterface;
use App\Infrastructure\Models\ServiceModel;
use Illuminate\Support\Str;

class CreateServiceUseCase
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepository
    ) {}

    public function execute(array $data): ServiceModel
    {
        $serviceData = [
            'id' => (string) Str::uuid(),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'duration' => $data['duration'],
            'price' => $data['price'],
            'salon_id' => $data['salon_id']
        ];

        return $this->serviceRepository->create($serviceData);
    }
}
