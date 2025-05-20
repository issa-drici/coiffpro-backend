<?php

namespace App\Application\Usecases\Salon;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\SalonRepositoryInterface;
use App\Exceptions\UnauthorizedException;
use App\Application\DTOs\SalonWithOwnerDTO;

class FindSalonByIdUsecase
{
    public function __construct(
        private SalonRepositoryInterface $salonRepository
    ) {}

    public function execute(string $salonId): SalonWithOwnerDTO
    {
        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Vérifier les permissions selon le rôle
        if (!in_array($user->role, ['admin', 'salon_owner', 'franchise_manager'])) {
            throw new UnauthorizedException("You do not have access to this restaurant.");
        }

        // 3. Si l'utilisateur n'est pas admin, vérifier qu'il est propriétaire
        if ($user->role === 'salon_owner') {
            $userSalon = $this->salonRepository->findByOwnerId($user->id);

            if (!$userSalon || $userSalon->getId() !== $salonId) {
                throw new UnauthorizedException("You do not have access to this salon.");
            }
        }

        // 4. Récupérer le salon avec les informations du propriétaire
        $salonWithOwner = $this->salonRepository->findByIdWithOwner($salonId);
        if (!$salonWithOwner) {
            throw new \Exception("Salon not found.");
        }

        return $salonWithOwner;
    }
}
