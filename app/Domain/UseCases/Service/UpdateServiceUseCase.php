<?php

namespace App\Domain\UseCases\Service;

use App\Domain\Repositories\Interfaces\ServiceRepositoryInterface;
use App\Infrastructure\Models\ServiceModel;

class UpdateServiceUseCase
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepository
    ) {}

    public function execute(string $serviceId, array $data): ?ServiceModel
    {
        $service = $this->serviceRepository->findById($serviceId);

        if (!$service) {
            return null;
        }

        $service->fill($data);
        return $this->serviceRepository->update($serviceId, $data);
    }
}
