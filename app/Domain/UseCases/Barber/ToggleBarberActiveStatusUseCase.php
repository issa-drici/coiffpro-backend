<?php

namespace App\Domain\UseCases\Barber;

use App\Domain\Repositories\Interfaces\BarberRepositoryInterface;
use App\Models\Barber;

class ToggleBarberActiveStatusUseCase
{
    public function __construct(
        private readonly BarberRepositoryInterface $barberRepository
    ) {}

    public function execute(string $barberId): Barber
    {
        $barber = $this->barberRepository->findById($barberId);

        if (!$barber) {
            throw new \DomainException('Barber non trouvé');
        }

        // Bascule l'état actif/inactif
        if ($barber->is_active) {
            return $this->barberRepository->deactivate($barberId);
        } else {
            return $this->barberRepository->activate($barberId);
        }
    }
}
