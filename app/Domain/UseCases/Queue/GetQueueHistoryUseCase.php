<?php

namespace App\Domain\UseCases\Queue;

use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use App\Domain\Repositories\Interfaces\SalonRepositoryInterface;
use Carbon\Carbon;

class GetQueueHistoryUseCase
{
    public function __construct(
        private readonly QueueClientRepositoryInterface $queueClientRepository,
        private readonly SalonRepositoryInterface $salonRepository
    ) {}

    public function execute(string $salonId, ?string $startDate = null, ?string $endDate = null, ?string $status = null): array
    {
        // Vérifier que le salon existe
        $salon = $this->salonRepository->findById($salonId);
        if (!$salon) {
            throw new \DomainException("Le salon avec l'ID $salonId n'existe pas.");
        }

        // Valider et parser les dates
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->subDays(7)->startOfDay();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        if ($start->isAfter($end)) {
            throw new \DomainException("La date de début doit être antérieure à la date de fin.");
        }

        // Valider le statut si fourni
        if ($status !== null && !in_array($status, ['waiting', 'in_progress', 'completed', 'cancelled'])) {
            throw new \DomainException("Statut invalide. Les statuts valides sont : waiting, in_progress, completed, cancelled.");
        }

        // Récupérer l'historique
        $history = $this->queueClientRepository->findHistoryBySalon($salonId, $start, $end, $status)->toArray();

        // Calculer les statistiques
        $stats = $this->calculateStats($history);

        return [
            'success' => true,
            'data' => [
                'salon' => [
                    'id' => $salon->id,
                    'name' => $salon->name
                ],
                'period' => [
                    'start' => $start->format('Y-m-d'),
                    'end' => $end->format('Y-m-d')
                ],
                'filters' => [
                    'status' => $status
                ],
                'statistics' => $stats,
                'clients' => array_map(function ($client) {
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
                        'completed_at' => $client['completed_at'] ?? null,
                        'cancelled_at' => $client['cancelled_at'] ?? null
                    ];
                }, $history)
            ]
        ];
    }

    private function calculateStats(array $clients): array
    {
        $stats = [
            'total' => count($clients),
            'by_status' => [
                'waiting' => 0,
                'in_progress' => 0,
                'completed' => 0,
                'cancelled' => 0
            ],
            'total_revenue' => 0,
            'average_service_duration' => 0,
            'total_duration' => 0
        ];

        foreach ($clients as $client) {
            // Compter par statut
            $stats['by_status'][$client['status']]++;

            // Calculer le revenu total (uniquement pour les clients complétés)
            if ($client['status'] === 'completed') {
                $stats['total_revenue'] += $client['amountToPay'];
            }

            // Calculer la durée totale des services
            $clientDuration = array_reduce($client['services'], function ($carry, $service) {
                return $carry + $service['duration'];
            }, 0);
            $stats['total_duration'] += $clientDuration;
        }

        // Calculer la durée moyenne des services
        if ($stats['total'] > 0) {
            $stats['average_service_duration'] = round($stats['total_duration'] / $stats['total']);
        }

        return $stats;
    }
}
