<?php

namespace App\Domain\UseCases\Service;

use App\Domain\Repositories\Interfaces\ServiceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GetAllServicesUseCase
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepository
    ) {}

    public function execute(): Collection
    {
        return $this->serviceRepository->findAll();
    }
}
