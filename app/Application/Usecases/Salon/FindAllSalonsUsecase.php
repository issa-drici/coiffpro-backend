<?php

namespace App\Application\Usecases\Salon;

use Illuminate\Support\Facades\Auth;
use App\Domain\Repositories\SalonRepositoryInterface;
use App\Exceptions\UnauthorizedException;

class FindAllSalonsUsecase
{
    public function __construct(
        private SalonRepositoryInterface $salonRepository
    ) {}

    public function execute(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        // 1. Vérifier l'authentification
        $user = Auth::user();
        if (!$user) {
            throw new UnauthorizedException("User not authenticated.");
        }

        // 2. Vérifier le rôle (seul l'admin peut voir tous les restaurants)
        if ($user->role !== 'admin') {
            throw new UnauthorizedException("Only administrators can access this resource.");
        }

        // 3. Nettoyer les filtres vides
        $filters = array_filter($filters, fn($value) => !is_null($value) && $value !== '');

        // 4. Récupérer tous les salons avec leurs propriétaires
        return $this->salonRepository->findAllWithOwnersPaginated($filters, $page, $perPage);
    }
}
