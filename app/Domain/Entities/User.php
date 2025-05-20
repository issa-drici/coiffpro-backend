<?php

namespace App\Domain\Entities;

class User
{
    public function __construct(
        private string $id,
        private string $name,
        private string $email,
        private string $role,
        private string $userPlan,
        private ?string $userSubscriptionStatus = null,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getUserPlan(): string
    {
        return $this->userPlan;
    }

    public function getUserSubscriptionStatus(): ?string
    {
        return $this->userSubscriptionStatus;
    }
} 