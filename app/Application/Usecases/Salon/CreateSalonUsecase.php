<?php

namespace App\Application\Usecases\Salon;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Domain\Entities\Salon;
use App\Domain\Repositories\SalonRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class CreateSalonUsecase
{
    public function __construct(
        private SalonRepositoryInterface $salonRepository
    ) {
        //
    }

    public function execute(array $data): Salon
    {
        // 1. Vérifier l’auth
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // // 2. Vérifier le rôle
        // if (!in_array($user->role, ['admin', 'salon_owner', 'franchise_manager'])) {
        //     throw new UnauthorizedException("User not allowed to create restaurant.");
        // }


        // 3. Créer l’entité
        $salon = new Salon(
            id: Str::uuid()->toString(),
            ownerId: $user->id,
            name: $user->name,
        );

        // 4. Persister
        return $this->salonRepository->create($salon);
    }
}
