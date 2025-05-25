<?php

namespace App\Domain\UseCases\Queue;

use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GetWaitingClientsUseCase
{
    public function __construct(
        private readonly QueueClientRepositoryInterface $queueClientRepository
    ) {}

    public function execute(string $salonId): Collection
    {
        return $this->queueClientRepository->findAllByStatus('waiting', $salonId);
    }
}
