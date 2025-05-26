<?php

namespace App\Domain\UseCases\Service;

use App\Domain\Repositories\Interfaces\ServiceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GetSalonServicesUseCase
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepository
    ) {}

    public function execute(string $salonId): Collection
    {
        return $this->serviceRepository->findAllBySalon($salonId);
    }
}
