<?php

namespace App\Domain\UseCases\Queue;

use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use App\Infrastructure\Models\QueueClientModel;
use Carbon\Carbon;

class GetQueueClientUseCase
{
    public function __construct(
        private readonly QueueClientRepositoryInterface $queueClientRepository
    ) {}

    public function execute(string $queueClientId): ?array
    {
        $queueClient = $this->queueClientRepository->findById($queueClientId);

        if (!$queueClient) {
            return null;
        }

        $salonId = $queueClient->salon_id;
        $currentTime = Carbon::now();

        // Récupérer le client en cours de service
        $currentClient = $this->queueClientRepository->findCurrentInProgress($salonId);
        $waitingClients = $this->queueClientRepository->findAllByStatus('waiting', $salonId)
            ->sortBy('created_at')
            ->values();

        // Construire la file d'attente complète (client en cours + waiting)
        $queue = collect();
        if ($currentClient) {
            $queue->push($currentClient);
        }
        foreach ($waitingClients as $client) {
            $queue->push($client);
        }

        // Trouver la position du client cible dans la file
        $position = null;
        $totalDuration = 0;
        $estimatedTime = $currentTime->copy();

        foreach ($queue as $index => $client) {
            if ($client->id == $queueClientId) {
                $position = $index + 1;
                break;
            }

            // Calculer le temps pour ce client
            if ($index === 0 && $currentClient && $client->id == $currentClient->id) {
                // Client en cours - calculer le temps restant
                $totalServiceDuration = $client->services->sum('duration');
                $startTime = Carbon::parse($client->updated_at); // Quand le service a commencé
                $elapsedTime = $currentTime->diffInMinutes($startTime);
                $remainingTime = max(0, $totalServiceDuration - $elapsedTime);
                $totalDuration += $remainingTime;
            } else {
                // Client en attente - ajouter sa durée totale
                $totalDuration += $client->services->sum('duration');
            }
        }

        $estimatedTime = $currentTime->copy()->addMinutes($totalDuration);

        // Retourner les infos du client + estimation harmonisée
        return [
            'id' => $queueClient->id,
            'ticket_number' => $queueClient->ticket_number,
            'position' => $position,
            'client' => [
                'id' => $queueClient->client->id,
                'firstName' => $queueClient->client->firstName,
                'lastName' => $queueClient->client->lastName,
                'phoneNumber' => $queueClient->client->phoneNumber
            ],
            'services' => $queueClient->services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'duration' => $service->duration,
                    'price' => $service->price
                ];
            })->toArray(),
            'status' => $queueClient->status,
            'amountToPay' => $queueClient->amountToPay,
            'notes' => $queueClient->notes,
            'created_at' => $queueClient->created_at,
            'estimatedTime' => $estimatedTime->toIso8601String(),
            'estimated_waiting_time' => $totalDuration
        ];
    }
}
