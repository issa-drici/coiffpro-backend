<?php

namespace App\Domain\Entities;

use App\Domain\Entities\User;
use App\Domain\Entities\Salon;

class Barber
{
    public function __construct(
        private readonly string $id,
        private readonly string $userId,
        private readonly string $salonId,
        private readonly ?string $bio,
        private readonly bool $isActive,
        private readonly ?string $isActiveChangedAt,
        private readonly ?User $user = null,
        private readonly ?Salon $salon = null,
        private readonly ?string $createdAt = null,
        private readonly ?string $updatedAt = null
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getSalonId(): string
    {
        return $this->salonId;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getIsActiveChangedAt(): ?string
    {
        return $this->isActiveChangedAt;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getSalon(): ?Salon
    {
        return $this->salon;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'user_id' => $this->userId,
            'salon_id' => $this->salonId,
            'bio' => $this->bio,
            'is_active' => $this->isActive,
            'is_active_changed_at' => $this->isActiveChangedAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];

        if ($this->user) {
            $data['user'] = [
                'id' => $this->user->getId(),
                'name' => $this->user->getName(),
                'email' => $this->user->getEmail(),
                'role' => $this->user->getRole()
            ];
        }

        if ($this->salon) {
            $data['salon'] = [
                'id' => $this->salon->getId(),
                'name' => $this->salon->getName(),
                'name_slug' => $this->salon->getNameSlug()
            ];
        }

        return $data;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            userId: $data['user_id'] ?? '',
            salonId: $data['salon_id'] ?? '',
            bio: $data['bio'] ?? null,
            isActive: $data['is_active'] ?? false,
            isActiveChangedAt: $data['is_active_changed_at'] ?? null,
            user: isset($data['user']) ? new User(
                id: $data['user']['id'] ?? '',
                name: $data['user']['name'] ?? '',
                email: $data['user']['email'] ?? '',
                role: $data['user']['role'] ?? '',
                userPlan: $data['user']['user_plan'] ?? 'basic',
                userSubscriptionStatus: $data['user']['user_subscription_status'] ?? null
            ) : null,
            salon: isset($data['salon']) ? new Salon(
                id: $data['salon']['id'] ?? '',
                ownerId: $data['salon']['owner_id'] ?? '',
                name: $data['salon']['name'] ?? '',
                nameSlug: $data['salon']['name_slug'] ?? null
            ) : null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }
}
