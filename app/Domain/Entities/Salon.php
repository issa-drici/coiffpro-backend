<?php

namespace App\Domain\Entities;

class Salon
{
    private string $id;
    private string $ownerId;
    private string $name;
    private ?string $address;
    private ?string $postalCode;
    private ?string $city;
    private ?string $citySlug;
    private ?string $typeSlug;
    private ?string $nameSlug;
    private ?string $phone;
    private ?string $logoId;
    private ?array $socialLinks;
    private ?array $googleInfo;

    public function __construct(
        string $id,
        string $ownerId,
        string $name,
        ?string $address = null,
        ?string $postalCode = null,
        ?string $city = null,
        ?string $citySlug = null,
        ?string $typeSlug = null,
        ?string $nameSlug = null,
        ?string $phone = null,
        ?string $logoId = null,
        ?array $socialLinks = null,
        ?array $googleInfo = null
    ) {
        $this->id = $id;
        $this->ownerId = $ownerId;
        $this->name = $name;
        $this->address = $address;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->citySlug = $citySlug;
        $this->typeSlug = $typeSlug;
        $this->nameSlug = $nameSlug;
        $this->phone = $phone;
        $this->logoId = $logoId;
        $this->socialLinks = $socialLinks;
        $this->googleInfo = $googleInfo;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getNameSlug(): ?string
    {
        return $this->nameSlug;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getLogoId(): ?string
    {
        return $this->logoId;
    }

    public function getSocialLinks(): ?array
    {
        return $this->socialLinks;
    }

    public function getGoogleInfo(): ?array
    {
        return $this->googleInfo;
    }

    public function setLogoId(?string $logoId): void
    {
        $this->logoId = $logoId;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function setNameSlug(?string $nameSlug): void
    {
        $this->nameSlug = $nameSlug;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function setSocialLinks(?array $socialLinks): void
    {
        $this->socialLinks = $socialLinks;
    }

    public function setGoogleInfo(?array $googleInfo): void
    {
        $this->googleInfo = $googleInfo;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getCitySlug(): ?string
    {
        return $this->citySlug;
    }

    public function setCitySlug(?string $citySlug): void
    {
        $this->citySlug = $citySlug;
    }

    public function getTypeSlug(): ?string
    {
        return $this->typeSlug;
    }

    public function setTypeSlug(?string $typeSlug): void
    {
        $this->typeSlug = $typeSlug;
    }
}
