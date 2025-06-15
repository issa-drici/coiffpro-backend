<?php

namespace App\Domain\UseCases\Queue;

use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use App\Domain\Repositories\Interfaces\SalonRepositoryInterface;
use Carbon\Carbon;

class GetCurrentClientUseCase
{
    public function __construct(
        private readonly QueueClientRepositoryInterface $queueClientRepository,
        private readonly SalonRepositoryInterface $salonRepository
    ) {}

    public function execute(string $salonId, string $barberId): array
    {
        // Vérifier que le salon existe
        $salon = $this->salonRepository->findById($salonId);
        if (!$salon) {
            throw new \DomainException("Le salon avec l'ID $salonId n'existe pas.");
        }

        // Récupérer le client actuellement en cours de service pour ce barber
        $currentClient = $this->queueClientRepository->findCurrentInProgress($salonId, $barberId);

        if (!$currentClient) {
            return [
                'success' => true,
                'data' => [
                    'salon' => [
                        'id' => $salon->getId(),
                        'name' => $salon->getName()
                    ],
                    'current_client' => null,
                    'next_client' => $this->getNextClient($salonId)
                ]
            ];
        }

        // Calculer le temps écoulé depuis le début du service
        $startTime = Carbon::parse($currentClient->updated_at);
        $elapsedTime = $startTime->diffInMinutes(Carbon::now());

        // Calculer le temps total estimé des services
        $totalEstimatedTime = $currentClient->services->sum('duration');

        // Calculer le temps restant estimé
        $remainingTime = max(0, $totalEstimatedTime - $elapsedTime);

        return [
            'success' => true,
            'data' => [
                'salon' => [
                    'id' => $salon->getId(),
                    'name' => $salon->getName()
                ],
                'current_client' => [
                    'id' => $currentClient->id,
                    'ticket_number' => $currentClient->ticket_number,
                    'client' => [
                        'id' => $currentClient->client->id,
                        'firstName' => $currentClient->client->firstName,
                        'lastName' => $currentClient->client->lastName,
                        'phoneNumber' => $currentClient->client->phoneNumber
                    ],
                    'services' => $currentClient->services->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'name' => $service->name,
                            'duration' => $service->duration,
                            'price' => $service->price
                        ];
                    })->toArray(),
                    'status' => $currentClient->status,
                    'amountToPay' => $currentClient->amountToPay,
                    'notes' => $currentClient->notes,
                    'started_at' => $currentClient->updated_at,
                    'elapsed_time' => $elapsedTime,
                    'total_estimated_time' => $totalEstimatedTime,
                    'remaining_time' => $remainingTime
                ],
                'next_client' => $this->getNextClient($salonId)
            ]
        ];
    }

    private function getNextClient(string $salonId): ?array
    {
        $nextClient = $this->queueClientRepository->findNextWaiting($salonId);

        if (!$nextClient) {
            return null;
        }

        // Vérifier que les relations sont chargées
        if (!$nextClient->client || !$nextClient->services) {
            return null;
        }

        // Calculer le temps d'attente estimé en utilisant sum() sur la Collection
        $estimatedWaitingTime = $nextClient->services->sum('duration');

        return [
            'id' => $nextClient->id,
            'ticket_number' => $nextClient->ticket_number,
            'client' => [
                'id' => $nextClient->client->id,
                'firstName' => $nextClient->client->firstName,
                'lastName' => $nextClient->client->lastName,
                'phoneNumber' => $nextClient->client->phoneNumber
            ],
            'services' => $nextClient->services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'duration' => $service->duration,
                    'price' => $service->price
                ];
            })->toArray(),
            'status' => $nextClient->status,
            'amountToPay' => $nextClient->amountToPay,
            'notes' => $nextClient->notes,
            'created_at' => $nextClient->created_at,
            'estimated_waiting_time' => $estimatedWaitingTime
        ];
    }
}
