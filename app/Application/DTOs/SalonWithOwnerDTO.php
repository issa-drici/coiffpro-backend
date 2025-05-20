<?php

namespace App\Application\DTOs;

class SalonWithOwnerDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $address,
        public ?string $phone,
        public ?array $logo,
        public ?array $social_links,
        public ?array $google_info,
        public ?string $postal_code,
        public ?string $city,
        public ?string $city_slug,
        public ?string $type_slug,
        public ?string $name_slug,
        public array $owner
    ) {}

    public static function fromSalonAndUser($salon, $file, $user): self
    {
        return new self(
            id: $salon->getId(),
            name: $salon->getName(),
            address: $salon->getAddress(),
            postal_code: $salon->getPostalCode(),
            city: $salon->getCity(),
            city_slug: $salon->getCitySlug(),
            type_slug: $salon->getTypeSlug(),
            name_slug: $salon->getNameSlug(),
            phone: $salon->getPhone(),
            logo: $file ? [
                'id' => $file->getId(),
                'url' => $file->getUrl()
            ] : null,
            social_links: $salon->getSocialLinks(),
            google_info: $salon->getGoogleInfo(),
            owner: [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'role' => $user->getRole(),
                'user_plan' => $user->getUserPlan(),
                'user_subscription_status' => $user->getUserSubscriptionStatus()
            ]
        );
    }
}
