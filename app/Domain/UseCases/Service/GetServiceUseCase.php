<?php

namespace App\Domain\UseCases\Service;

use App\Domain\Repositories\Interfaces\ServiceRepositoryInterface;
use App\Infrastructure\Models\ServiceModel;

class GetServiceUseCase
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepository
    ) {}

    public function execute(string $serviceId): ?ServiceModel
    {
        return $this->serviceRepository->findById($serviceId);
    }
}
