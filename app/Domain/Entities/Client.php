<?php

namespace App\Domain\Entities;

class Client
{
    public function __construct(
        private readonly string $id,
        private readonly string $firstName,
        private readonly string $lastName,
        private readonly string $phoneNumber,
        private readonly ?string $email,
        private readonly string $salonId,
        private readonly ?string $createdAt = null,
        private readonly ?string $updatedAt = null
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getSalonId(): string
    {
        return $this->salonId;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }
}
