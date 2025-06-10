<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\Interfaces\SalonRepositoryInterface;
use App\Domain\Entities\Salon;
use App\Application\DTOs\SalonWithOwnerDTO;
use App\Infrastructure\Models\SalonModel;
use App\Domain\Entities\User;
use App\Domain\Entities\File;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SalonRepository implements SalonRepositoryInterface
{
    public function __construct(
        private readonly SalonModel $model
    ) {}

    public function findById(string $id): ?Salon
    {
        $model = $this->model->find($id);
        if (!$model) {
            return null;
        }
        return $this->toDomainEntity($model);
    }

    public function findAll(): Collection
    {
        return $this->model->all();
    }

    public function create(Salon $salon): Salon
    {
        $model = $this->model->create([
            'id'          => $salon->getId(),
            'owner_id'    => $salon->getOwnerId(),
            'name'        => $salon->getName(),
            'address'     => $salon->getAddress(),
            'postal_code' => $salon->getPostalCode(),
            'city'        => $salon->getCity(),
            'city_slug'   => $salon->getCitySlug(),
            'name_slug'   => $salon->getNameSlug(),
            'type_slug'   => $salon->getTypeSlug(),
            'phone'       => $salon->getPhone(),
        ]);
        return $this->toDomainEntity($model);
    }

    public function update(Salon $salon): Salon
    {
        $model = $this->model->findOrFail($salon->getId());

        $model->update([
            'name' => $salon->getName(),
            'address' => $salon->getAddress(),
            'postal_code' => $salon->getPostalCode(),
            'city' => $salon->getCity(),
            'city_slug' => $salon->getCitySlug(),
            'name_slug' => $salon->getNameSlug(),
            'type_slug' => $salon->getTypeSlug(),
            'phone' => $salon->getPhone(),
            'logo_id' => $salon->getLogoId(),
            'social_links' => $salon->getSocialLinks(),
            'google_info' => $salon->getGoogleInfo(),
        ]);

        return $this->toDomainEntity($model);
    }

    public function findByOwnerId(string $ownerId): ?Salon
    {
        $model = $this->model->where('owner_id', $ownerId)->first();
        if (!$model) {
            return null;
        }

        return $this->toDomainEntity($model);
    }

    public function findByIdWithOwner(string $id): ?SalonWithOwnerDTO
    {
        $salon = $this->model->with(['owner', 'logo'])
            ->where('salons.id', $id)
            ->join('users', 'salons.owner_id', '=', 'users.id')
            ->select(
                'salons.*',
                DB::raw('CONCAT(users."firstName", \' \', users."lastName") as owner_name'),
                'users.email as owner_email',
                'users.role as owner_role',
                'users.user_plan as owner_plan',
                'users.user_subscription_status as owner_subscription_status'
            )
            ->first();

        if (!$salon) {
            return null;
        }

        $file = null;
        if ($salon->logo) {
            $file = new File(
                id: $salon->logo->id,
                userId: $salon->logo->user_id,
                path: $salon->logo->path,
                url: $salon->logo->url,
                filename: $salon->logo->filename,
                mimeType: $salon->logo->mime_type,
                size: $salon->logo->size,
                createdAt: $salon->logo->created_at,
                updatedAt: $salon->logo->updated_at
            );
        }

        return SalonWithOwnerDTO::fromSalonAndUser(
            $this->toDomainEntity($salon),
            $file,
            new User(
                id: $salon->owner_id,
                name: $salon->owner_name,
                email: $salon->owner_email,
                role: $salon->owner_role,
                userPlan: $salon->owner_plan,
                userSubscriptionStatus: $salon->owner_subscription_status
            )
        );
    }

    public function findAllWithOwners(): array
    {
        $salons = $this->model->with('logo')
            ->join('users', 'salons.owner_id', '=', 'users.id')
            ->select(
                'salons.*',
                DB::raw('CONCAT(users."firstName", \' \', users."lastName") as owner_name'),
                'users.email as owner_email',
                'users.role as owner_role',
                'users.user_plan as owner_plan',
                'users.user_subscription_status as owner_subscription_status'
            )
            ->get();

        return $salons->map(function ($model) {
            $file = null;
            if ($model->logo) {
                $file = new File(
                    id: $model->logo->id,
                    userId: $model->logo->user_id,
                    path: $model->logo->path,
                    url: $model->logo->url,
                    filename: $model->logo->filename,
                    mimeType: $model->logo->mime_type,
                    size: $model->logo->size,
                    createdAt: $model->logo->created_at,
                    updatedAt: $model->logo->updated_at
                );
            }

            return SalonWithOwnerDTO::fromSalonAndUser(
                $this->toDomainEntity($model),
                $file,
                new User(
                    id: $model->owner_id,
                    name: $model->owner_name,
                    email: $model->owner_email,
                    role: $model->owner_role,
                    userPlan: $model->owner_plan,
                    userSubscriptionStatus: $model->owner_subscription_status
                )
            );
        })->all();
    }

    public function findAllWithOwnersPaginated(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $query = $this->model->query()
            ->with('logo')
            ->join('users', 'salons.owner_id', '=', 'users.id')
            ->select(
                'salons.*',
                DB::raw('CONCAT(users."firstName", \' \', users."lastName") as owner_name'),
                'users.email as owner_email',
                'users.role as owner_role',
                'users.user_plan as owner_plan',
                'users.user_subscription_status as owner_subscription_status'
            );

        // Appliquer les filtres
        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'name':
                    $query->whereRaw('LOWER(salons.name) LIKE ?', ['%' . strtolower($value) . '%']);
                    break;
                case 'address':
                    $query->where('salons.address', 'like', "%{$value}%");
                    break;
                case 'phone':
                    $query->where('salons.phone', 'like', "%{$value}%");
                    break;
                case 'owner_name':
                    $query->whereRaw('LOWER(CONCAT(users."firstName", \' \', users."lastName")) LIKE ?', ['%' . strtolower($value) . '%']);
                    break;
                case 'owner_email':
                    $query->where('users.email', 'like', "%{$value}%");
                    break;
                case 'owner_plan':
                    $query->where('users.user_plan', $value);
                    break;
            }
        }

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);
        $salons = $paginator->items();

        return [
            'data' => array_map(function ($model) {
                $file = null;
                if ($model->logo) {
                    $file = new File(
                        id: $model->logo->id,
                        userId: $model->logo->user_id,
                        path: $model->logo->path,
                        url: $model->logo->url,
                        filename: $model->logo->filename,
                        mimeType: $model->logo->mime_type,
                        size: $model->logo->size,
                        createdAt: $model->logo->created_at,
                        updatedAt: $model->logo->updated_at
                    );
                }

                return SalonWithOwnerDTO::fromSalonAndUser(
                    $this->toDomainEntity($model),
                    $file,
                    new User(
                        id: $model->owner_id,
                        name: $model->owner_name,
                        email: $model->owner_email,
                        role: $model->owner_role,
                        userPlan: $model->owner_plan,
                        userSubscriptionStatus: $model->owner_subscription_status
                    )
                );
            }, $salons),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage()
        ];
    }

    public function findBySlug(string $typeSlug, string $citySlug, string $nameSlug): ?Salon
    {
        $model = $this->model->where('type_slug', $typeSlug)
            ->where('city_slug', $citySlug)
            ->where('name_slug', $nameSlug)
            ->first();

        if (!$model) {
            return null;
        }

        return $this->toDomainEntity($model);
    }

    private function toDomainEntity(SalonModel $model): Salon
    {
        return new Salon(
            id: $model->id,
            ownerId: $model->owner_id,
            name: $model->name,
            address: $model->address,
            city: $model->city,
            citySlug: $model->city_slug,
            nameSlug: $model->name_slug,
            typeSlug: $model->type_slug,
            postalCode: $model->postal_code,
            phone: $model->phone,
            logoId: $model->logo_id,
            socialLinks: $model->social_links,
            googleInfo: $model->google_info
        );
    }
}
