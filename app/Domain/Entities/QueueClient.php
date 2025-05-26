<?php

namespace App\Domain\Entities;

use App\Domain\Entities\Client;
use App\Domain\Entities\Salon;
use App\Domain\Entities\Service;
use Illuminate\Database\Eloquent\Collection;

class QueueClient
{
    public function __construct(
        private readonly string $id,
        private readonly string $clientId,
        private readonly string $salonId,
        private readonly string $status,
        private readonly float $amountToPay,
        private readonly ?string $notes,
        private readonly int $ticketNumber,
        private readonly ?Client $client = null,
        private readonly ?Salon $salon = null,
        private readonly ?Collection $services = null,
        private readonly ?string $createdAt = null,
        private readonly ?string $updatedAt = null
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getSalonId(): string
    {
        return $this->salonId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getAmountToPay(): float
    {
        return $this->amountToPay;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function getTicketNumber(): int
    {
        return $this->ticketNumber;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function getSalon(): ?Salon
    {
        return $this->salon;
    }

    public function getServices(): ?Collection
    {
        return $this->services;
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
            'client_id' => $this->clientId,
            'salon_id' => $this->salonId,
            'status' => $this->status,
            'amountToPay' => $this->amountToPay,
            'notes' => $this->notes,
            'ticket_number' => $this->ticketNumber,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];

        if ($this->client) {
            $data['client'] = [
                'id' => $this->client->getId(),
                'firstName' => $this->client->getFirstName(),
                'lastName' => $this->client->getLastName(),
                'email' => $this->client->getEmail(),
                'phoneNumber' => $this->client->getPhoneNumber()
            ];
        }

        if ($this->salon) {
            $data['salon'] = [
                'id' => $this->salon->getId(),
                'name' => $this->salon->getName()
            ];
        }

        if ($this->services) {
            $data['services'] = $this->services->map(function ($service) {
                return [
                    'id' => $service->getId(),
                    'name' => $service->getName(),
                    'price' => $service->getPrice(),
                    'duration' => $service->getDuration()
                ];
            })->toArray();
        }

        return $data;
    }
}
