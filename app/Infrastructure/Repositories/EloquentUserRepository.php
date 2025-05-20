<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Models\User as UserModel;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function update(User $user): User
    {
        $model = UserModel::findOrFail($user->getId());
        
        $model->update([
            'name' => $user->getName(),
            // Ajoutez d'autres champs si nécessaire
        ]);

        return $this->toDomainEntity($model);
    }

    private function toDomainEntity(UserModel $model): User
    {
        return new User(
            id: $model->id,
            name: $model->name,
            email: $model->email,
            role: $model->role,
            userPlan: $model->user_plan,
            userSubscriptionStatus: $model->user_subscription_status
        );
    }
} 