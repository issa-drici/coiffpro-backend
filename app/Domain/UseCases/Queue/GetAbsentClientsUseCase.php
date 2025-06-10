<?php

namespace App\Domain\UseCases\Queue;

use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use App\Domain\Repositories\Interfaces\SalonRepositoryInterface;
use Carbon\Carbon;

class GetAbsentClientsUseCase
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

        // Récupérer les clients absents pour ce salon à cette date
        $absentClients = $this->queueClientRepository->findAllByStatus('absent', $salonId)
            ->filter(function ($client) use ($targetDate) {
                return $client->created_at->format('Y-m-d') === $targetDate->format('Y-m-d');
            })
            ->sortBy('created_at')
            ->values()
            ->toArray();

        return [
            'success' => true,
            'data' => [
                'salon' => [
                    'id' => $salon->getId(),
                    'name' => $salon->getName()
                ],
                'date' => $targetDate->format('Y-m-d'),
                'clients' => array_map(function ($client, $index) {
                    return [
                        'id' => $client['id'],
                        'ticket_number' => $client['ticket_number'],
                        'position' => $index + 1,
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
                        'updated_at' => $client['updated_at']
                    ];
                }, $absentClients, array_keys($absentClients))
            ]
        ];
    }
}
