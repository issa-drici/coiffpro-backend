<?php

namespace App\Domain\UseCases\Queue;

use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use App\Infrastructure\Models\QueueClientModel;

class GetCurrentClientUseCase
{
    public function __construct(
        private readonly QueueClientRepositoryInterface $queueClientRepository
    ) {}

    public function execute(string $salonId): ?QueueClientModel
    {
        return $this->queueClientRepository->findCurrentInProgress($salonId);
    }
}
