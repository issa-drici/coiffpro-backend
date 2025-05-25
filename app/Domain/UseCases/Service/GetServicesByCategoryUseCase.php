<?php

namespace App\Domain\UseCases\Service;

use App\Domain\Repositories\Interfaces\ServiceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GetServicesByCategoryUseCase
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepository
    ) {}

    public function execute(string $category, string $salonId): Collection
    {
        return $this->serviceRepository->findAllByCategory($category, $salonId);
    }
}
