<?php

namespace App\Application\Usecases\Salon;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Domain\Entities\Salon;
use App\Domain\Repositories\Interfaces\SalonRepositoryInterface;
use App\Application\Usecases\File\CreateFileUsecase;
use App\Application\Usecases\File\DeleteFileUsecase;
use App\Exceptions\UnauthorizedException;
use App\Services\S3Service;
use Illuminate\Support\Str;
use App\Domain\Repositories\UserRepositoryInterface;

class UpdateSalonUsecase
{
    public function __construct(
        private SalonRepositoryInterface $salonRepository,
        private CreateFileUsecase $createFileUsecase,
        private DeleteFileUsecase $deleteFileUsecase,
        private S3Service $s3Service,
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function execute(string $salonId, array $data): Salon
    {
        // Démarrer une transaction
        return DB::transaction(function () use ($salonId, $data) {
            try {
                // 1. Auth
                $user = Auth::user();
                if (!$user) {
                    throw new UnauthorizedException("User not authenticated.");
                }

                // 2. Retrouver le salon
                $salon = $this->salonRepository->findById($salonId);
                if (!$salon) {
                    throw new \Exception("Salon not found.");
                }

                // 3. Vérifier les permissions
                if (!in_array($user->role, ['admin', 'franchise_manager'])) {
                    if ($salon->getOwnerId() !== $user->id) {
                        throw new UnauthorizedException("You do not have permission to update this salon.");
                    }
                }

                // Variables pour le rollback si nécessaire
                $oldLogoId = $salon->getLogoId();
                $newLogoId = null;
                $newLogoPath = null;

                // 4. Gérer le logo
                if (isset($data['logo'])) {
                    try {
                        // Générer un UUID unique pour le dossier
                        $folderUuid = Str::uuid()->toString();
                        $securePath = "salons/logos/{$folderUuid}";

                        // 1. Créer le nouveau fichier dans le dossier sécurisé
                        $file = $this->createFileUsecase->execute($data['logo'], $securePath);
                        $newLogoId = $file->getId();
                        $newLogoPath = $file->getPath();

                        // 2. Stocker l'ancien ID pour suppression ultérieure
                        $oldLogoId = $salon->getLogoId();

                        // 3. Mettre à jour la référence dans le salon
                        $salon->setLogoId($newLogoId);
                        $this->salonRepository->update($salon);

                        // 4. Une fois la référence mise à jour, supprimer l'ancien logo
                        if ($oldLogoId) {
                            $this->deleteFileUsecase->execute($oldLogoId);
                        }
                    } catch (\Exception $e) {
                        // En cas d'erreur, nettoyer le nouveau fichier si créé
                        if ($newLogoPath) {
                            $this->s3Service->deleteFile($newLogoPath);
                        }
                        throw $e;
                    }
                } elseif (isset($data['remove_logo']) && $data['remove_logo'] === 'true') {
                    if ($oldLogoId = $salon->getLogoId()) {
                        // 1. D'abord mettre à null la référence dans le salon
                        $salon->setLogoId(null);
                        $this->salonRepository->update($salon);

                        // 2. Ensuite supprimer le fichier
                        $this->deleteFileUsecase->execute($oldLogoId);
                    }
                }

                // 5. Mettre à jour les autres champs
                if (isset($data['name'])) {
                    $salon->setName($data['name']);

                    // Mettre à jour le nom de l'utilisateur si c'est le propriétaire
                    if ($salon->getOwnerId() === $user->id) {
                        // Créer une entité User du domaine
                        $userEntity = new \App\Domain\Entities\User(
                            id: $user->id,
                            name: $data['name'],
                            email: $user->email,
                            role: $user->role,
                            userPlan: $user->user_plan,
                            userSubscriptionStatus: $user->user_subscription_status
                        );
                        $this->userRepository->update($userEntity);
                    }

                    $salon->setNameSlug(Str::slug($data['name']));
                }
                if (isset($data['address'])) {
                    $salon->setAddress($data['address']);
                }
                if (isset($data['postal_code'])) {
                    $salon->setPostalCode($data['postal_code']);
                }
                if (isset($data['city'])) {
                    $salon->setCity($data['city']);
                    // Générer automatiquement le city_slug à partir de la ville
                    $salon->setCitySlug(Str::slug($data['city']));
                }
                if (isset($data['type_slug'])) {
                    $salon->setTypeSlug($data['type_slug']);
                }
                if (isset($data['phone'])) {
                    $salon->setPhone($data['phone']);
                }
                if (isset($data['social_links'])) {
                    $salon->setSocialLinks($data['social_links']);
                }
                if (isset($data['google_info'])) {
                    $salon->setGoogleInfo($data['google_info']);
                }

                // 6. Sauvegarder
                return $this->salonRepository->update($salon);
            } catch (\Exception $e) {
                // En cas d'erreur, la transaction sera automatiquement annulée
                throw $e;
            }
        });
    }
}
