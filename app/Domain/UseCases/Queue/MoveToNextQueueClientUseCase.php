<?php

namespace App\Domain\UseCases\Queue;

use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use App\Domain\Repositories\Interfaces\SalonRepositoryInterface;
use Carbon\Carbon;

class MoveToNextQueueClientUseCase
{
    public function __construct(
        private readonly QueueClientRepositoryInterface $queueClientRepository,
        private readonly SalonRepositoryInterface $salonRepository
    ) {}

    public function execute(string $salonId): array
    {
        // Vérifier que le salon existe
        $salon = $this->salonRepository->findById($salonId);
        if (!$salon) {
            throw new \DomainException("Le salon avec l'ID $salonId n'existe pas.");
        }

        // Récupérer le client actuellement en cours de service
        $currentClient = $this->queueClientRepository->findCurrentInProgress($salonId);

        // Si un client est en cours, le marquer comme terminé
        if ($currentClient) {
            $this->queueClientRepository->updateStatus($currentClient->id, 'completed');
        }

        // Récupérer le prochain client en attente
        $nextClient = $this->queueClientRepository->findNextWaiting($salonId);

        if (!$nextClient) {
            return [
                'success' => true,
                'data' => [
                    'salon' => [
                        'id' => $salon->getId(),
                        'name' => $salon->getName()
                    ],
                    'previous_client' => $currentClient ? [
                        'id' => $currentClient->id,
                        'ticket_number' => $currentClient->ticket_number,
                        'client' => [
                            'id' => $currentClient->client->id,
                            'firstName' => $currentClient->client->firstName,
                            'lastName' => $currentClient->client->lastName
                        ],
                        'status' => 'completed',
                        'completed_at' => Carbon::now()->toDateTimeString()
                    ] : null,
                    'current_client' => null,
                    'next_client' => null,
                    'message' => 'Aucun client en attente'
                ]
            ];
        }

        // Mettre à jour le statut du prochain client
        $this->queueClientRepository->updateStatus($nextClient->id, 'in_progress');

        // Calculer le temps d'attente
        $waitingTime = Carbon::parse($nextClient->created_at)->diffInMinutes(Carbon::now());

        // Calculer le temps total estimé des services
        $totalEstimatedTime = $nextClient->services->sum('duration');

        return [
            'success' => true,
            'data' => [
                'salon' => [
                    'id' => $salon->getId(),
                    'name' => $salon->getName()
                ],
                'previous_client' => $currentClient ? [
                    'id' => $currentClient->id,
                    'ticket_number' => $currentClient->ticket_number,
                    'client' => [
                        'id' => $currentClient->client->id,
                        'firstName' => $currentClient->client->firstName,
                        'lastName' => $currentClient->client->lastName
                    ],
                    'status' => 'completed',
                    'completed_at' => Carbon::now()->toDateTimeString()
                ] : null,
                'current_client' => [
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
                    'status' => 'in_progress',
                    'amountToPay' => $nextClient->amountToPay,
                    'notes' => $nextClient->notes,
                    'started_at' => Carbon::now()->toDateTimeString(),
                    'waiting_time' => $waitingTime,
                    'total_estimated_time' => $totalEstimatedTime
                ],
                'next_client' => $this->getNextWaitingClient($salonId)
            ]
        ];
    }

    private function getNextWaitingClient(string $salonId): ?array
    {
        $nextWaitingClient = $this->queueClientRepository->findNextWaiting($salonId);

        if (!$nextWaitingClient) {
            return null;
        }

        // Calculer le temps d'attente estimé
        $estimatedWaitingTime = $nextWaitingClient->services->sum('duration');

        return [
            'id' => $nextWaitingClient->id,
            'ticket_number' => $nextWaitingClient->ticket_number,
            'client' => [
                'id' => $nextWaitingClient->client->id,
                'firstName' => $nextWaitingClient->client->firstName,
                'lastName' => $nextWaitingClient->client->lastName,
                'phoneNumber' => $nextWaitingClient->client->phoneNumber
            ],
            'services' => $nextWaitingClient->services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'duration' => $service->duration,
                    'price' => $service->price
                ];
            })->toArray(),
            'status' => $nextWaitingClient->status,
            'amountToPay' => $nextWaitingClient->amountToPay,
            'notes' => $nextWaitingClient->notes,
            'created_at' => $nextWaitingClient->created_at,
            'estimated_waiting_time' => $estimatedWaitingTime
        ];
    }
}
