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

    public function execute(string $salonId, ?string $date = null): array
    {
        // Vérifier que le salon existe
        $salon = $this->salonRepository->findById($salonId);
        if (!$salon) {
            throw new \DomainException("Le salon avec l'ID $salonId n'existe pas.");
        }

        // Utiliser la date fournie ou aujourd'hui
        $targetDate = $date ? Carbon::parse($date) : Carbon::now();

        // Récupérer tous les clients en attente pour ce salon à cette date
        $waitingClients = $this->queueClientRepository->findAllBySalonAndDate($salonId, $targetDate)->toArray();

        // Calculer le temps d'attente estimé pour chaque client
        $estimatedWaitingTimes = $this->calculateEstimatedWaitingTimes($waitingClients);

        return [
            'success' => true,
            'data' => [
                'salon' => [
                    'id' => $salon->id,
                    'name' => $salon->name
                ],
                'date' => $targetDate->format('Y-m-d'),
                'clients' => array_map(function ($client) use ($estimatedWaitingTimes) {
                    return [
                        'id' => $client['id'],
                        'ticket_number' => $client['ticket_number'],
                        'client' => [
                            'id' => $client['client']['id'],
                            'firstName' => $client['client']['firstName'],
                            'lastName' => $client['client']['lastName'],
                            'phoneNumber' => $client['client']['phoneNumber']
                        ],
                        'services' => array_map(function ($service) {
                            return [
                                'id' => $service['id'],
                                'name' => $service['name'],
                                'duration' => $service['duration'],
                                'price' => $service['price']
                            ];
                        }, $client['services']),
                        'status' => $client['status'],
                        'amountToPay' => $client['amountToPay'],
                        'notes' => $client['notes'],
                        'created_at' => $client['created_at'],
                        'estimated_waiting_time' => $estimatedWaitingTimes[$client['id']] ?? null
                    ];
                }, $waitingClients)
            ]
        ];
    }

    private function calculateEstimatedWaitingTimes(array $clients): array
    {
        $waitingTimes = [];
        $totalDuration = 0;

        foreach ($clients as $client) {
            if ($client['status'] !== 'waiting') {
                continue;
            }

            // Calculer la durée totale des services pour ce client
            $clientDuration = array_reduce($client['services'], function ($carry, $service) {
                return $carry + $service['duration'];
            }, 0);

            // Le temps d'attente estimé est la somme des durées des clients qui précèdent
            $waitingTimes[$client['id']] = $totalDuration;
            $totalDuration += $clientDuration;
        }

        return $waitingTimes;
    }
}
