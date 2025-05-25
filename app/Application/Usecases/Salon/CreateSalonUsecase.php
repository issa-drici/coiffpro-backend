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
        // 1. Vérifier l'auth
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Vérifier que le nom du salon est fourni
        if (!isset($data['name']) || empty($data['name'])) {
            throw new \InvalidArgumentException("Le nom du salon est obligatoire.");
        }

        // 3. Créer l'entité
        $salon = new Salon(
            id: Str::uuid()->toString(),
            ownerId: $data['owner_id'] ?? $user->id,
            name: $data['name'],
            nameSlug: $data['name_slug'] ?? Str::slug($data['name'])
        );

        // 4. Persister
        return $this->salonRepository->create($salon);
    }
}
