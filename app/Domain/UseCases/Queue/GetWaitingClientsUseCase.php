<?php

namespace App\Domain\UseCases\Queue;

use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use App\Domain\Repositories\Interfaces\SalonRepositoryInterface;
use Carbon\Carbon;

class GetWaitingClientsUseCase
{
    public function __construct(
        private readonly QueueClientRepositoryInterface $queueClientRepository,
        private readonly SalonRepositoryInterface $salonRepository
    ) {}

    public function execute(string $salonId, string $barberId, ?string $date = null): array
    {
        // Vérifier que le salon existe
        $salon = $this->salonRepository->findById($salonId);
        if (!$salon) {
            throw new \DomainException("Le salon avec l'ID $salonId n'existe pas.");
        }

        // Utiliser la date fournie ou aujourd'hui
        $targetDate = $date ? Carbon::parse($date) : Carbon::now();

        // Récupérer le client en cours de service pour ce barber
        $currentClient = $this->queueClientRepository->findCurrentInProgress($salonId, $barberId);

        // Récupérer uniquement les clients en attente pour ce barber à cette date
        $waitingClients = $this->queueClientRepository->findAllByStatusAndBarber('waiting', $salonId, $barberId)
            ->filter(function ($client) use ($targetDate) {
                return $client->created_at->format('Y-m-d') === $targetDate->format('Y-m-d');
            })
            ->sortBy('created_at')
            ->values();

        // Calculer le temps d'attente estimé en tenant compte de l'état actuel de la file
        $estimatedWaitingTimes = $this->calculateEstimatedWaitingTimesWithCurrentState($waitingClients, $currentClient);

        // Calculer l'heure estimée de début de service
        $currentTime = Carbon::now();
        $estimatedStartTimes = $this->calculateEstimatedStartTimes($estimatedWaitingTimes, $currentTime);

        return [
            'success' => true,
            'data' => [
                'salon' => [
                    'id' => $salon->getId(),
                    'name' => $salon->getName()
                ],
                'date' => $targetDate->format('Y-m-d'),
                'clients' => $waitingClients->map(function ($client, $index) use ($estimatedWaitingTimes, $estimatedStartTimes) {
                    return [
                        'id' => $client->id,
                        'ticket_number' => $client->ticket_number,
                        'position' => $index + 1, // Position basée sur l'index (1-based)
                        'client' => [
                            'id' => $client->client->id,
                            'firstName' => $client->client->firstName,
                            'lastName' => $client->client->lastName,
                            'phoneNumber' => $client->client->phoneNumber
                        ],
                        'services' => $client->services->map(function ($service) {
                            return [
                                'id' => $service->id,
                                'name' => $service->name,
                                'duration' => $service->duration,
                                'price' => $service->price
                            ];
                        })->toArray(),
                        'status' => $client->status,
                        'amountToPay' => $client->amountToPay,
                        'notes' => $client->notes,
                        'created_at' => $client->created_at,
                        'estimated_waiting_time' => $estimatedWaitingTimes[$client->id] ?? null,
                        'estimatedTime' => $estimatedStartTimes[$client->id] ?? null
                    ];
                })->toArray()
            ]
        ];
    }

    private function calculateEstimatedWaitingTimesWithCurrentState($waitingClients, $currentClient): array
    {
        $waitingTimes = [];
        $totalDuration = 0;
        $currentTime = Carbon::now();

        // Si un client est en cours, calculer le temps restant de ses services
        if ($currentClient) {
            // Durée totale des services du client en cours
            $totalServiceDuration = $currentClient->services->sum('duration');

            // Temps déjà passé depuis le début du service
            $elapsedTime = Carbon::parse($currentClient->updated_at)->diffInMinutes($currentTime);

            // Temps restant pour le client en cours
            $remainingTime = max(0, $totalServiceDuration - $elapsedTime);

            // Ajouter le temps restant du client en cours au temps total
            $totalDuration += $remainingTime;
        }

        // Calculer le temps d'attente pour chaque client en attente
        foreach ($waitingClients as $client) {
            // Le temps d'attente estimé est la somme des durées des clients qui précèdent
            $waitingTimes[$client->id] = $totalDuration;

            // Ajouter la durée des services de ce client au temps total
            $totalDuration += $client->services->sum('duration');
        }

        return $waitingTimes;
    }

    private function calculateEstimatedWaitingTimes($clients): array
    {
        $waitingTimes = [];
        $totalDuration = 0;

        foreach ($clients as $client) {
            // Calculer la durée totale des services pour ce client
            $clientDuration = $client->services->sum('duration');

            // Le temps d'attente estimé est la somme des durées des clients qui précèdent
            $waitingTimes[$client->id] = $totalDuration;
            $totalDuration += $clientDuration;
        }

        return $waitingTimes;
    }

    private function calculateEstimatedStartTimes(array $estimatedWaitingTimes, Carbon $currentTime): array
    {
        $estimatedStartTimes = [];

        foreach ($estimatedWaitingTimes as $clientId => $waitingMinutes) {
            // Ajouter le temps d'attente estimé à l'heure actuelle
            $estimatedStartTimes[$clientId] = $currentTime->copy()->addMinutes($waitingMinutes)->toIso8601String();
        }

        return $estimatedStartTimes;
    }
}
