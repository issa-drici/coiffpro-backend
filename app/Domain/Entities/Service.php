<?php

namespace App\Domain\Entities;

class Service
{
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly float $price,
        private readonly int $duration,
        private readonly ?string $description,
        private readonly ?string $category,
        private readonly string $salonId,
        private readonly ?string $createdAt = null,
        private readonly ?string $updatedAt = null
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCategory(): ?string
    {
        return $this->category;
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
