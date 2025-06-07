<?php

namespace App\Domain\UseCases\Queue;

use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use App\Domain\Repositories\Interfaces\SalonRepositoryInterface;
use Carbon\Carbon;

class GetQueueUseCase
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

        // Récupérer tous les clients de la file d'attente du jour
        $clients = $this->queueClientRepository->findAllBySalonAndDate($salonId, Carbon::now());

        // Trier les clients par statut et heure d'arrivée
        $sortedClients = $this->sortClientsByStatusAndTime($clients);

        // Calculer les positions et les temps estimés
        $queue = $this->calculatePositionsAndEstimatedTimes($sortedClients);

        return [
            'success' => true,
            'data' => $queue
        ];
    }

    private function sortClientsByStatusAndTime($clients): array
    {
        $sortedClients = $clients->toArray();

        // Trier d'abord par statut (in_progress, waiting, completed)
        usort($sortedClients, function ($a, $b) {
            $statusOrder = [
                'in_progress' => 0,
                'waiting' => 1,
                'completed' => 2
            ];

            if ($statusOrder[$a['status']] !== $statusOrder[$b['status']]) {
                return $statusOrder[$a['status']] - $statusOrder[$b['status']];
            }

            // Si même statut, trier par heure d'arrivée
            return strtotime($a['created_at']) - strtotime($b['created_at']);
        });

        return $sortedClients;
    }

    private function calculatePositionsAndEstimatedTimes(array $clients): array
    {
        $queue = [];
        $currentTime = Carbon::now();
        $position = 1;

        foreach ($clients as $client) {
            // Calculer la durée totale des services
            $totalDuration = array_reduce($client['services'], function ($carry, $service) {
                return $carry + $service['duration'];
            }, 0);

            // Calculer le temps estimé de début
            $estimatedStartTime = $this->calculateEstimatedStartTime($clients, $client, $currentTime);

            $queue[] = [
                'id' => $client['id'],
                'firstName' => $client['client']['firstName'],
                'lastName' => $client['client']['lastName'] ?? null,
                'phoneNumber' => $client['client']['phoneNumber'],
                'services' => array_map(function ($service) {
                    return $service['name'];
                }, $client['services']),
                'registrationTime' => $client['created_at'],
                'position' => $position,
                'estimatedTime' => $estimatedStartTime->toIso8601String(),
                'status' => $client['status'],
                'estimatedDuration' => $totalDuration,
                'amountToPay' => $client['amountToPay'] ?? null
            ];

            $position++;
        }

        return $queue;
    }

    private function calculateEstimatedStartTime(array $clients, array $currentClient, Carbon $currentTime): Carbon
    {
        // Si le client est en cours, son temps estimé est maintenant
        if ($currentClient['status'] === 'in_progress') {
            return $currentTime;
        }

        // Si le client est terminé, son temps estimé est son heure de début
        if ($currentClient['status'] === 'completed') {
            return Carbon::parse($currentClient['updated_at']);
        }

        // Pour les clients en attente, calculer le temps estimé en fonction des clients précédents
        $estimatedTime = clone $currentTime;

        foreach ($clients as $client) {
            // S'arrêter quand on atteint le client actuel
            if ($client['id'] === $currentClient['id']) {
                break;
            }

            // Ignorer les clients terminés
            if ($client['status'] === 'completed') {
                continue;
            }

            // Ajouter la durée des services du client précédent
            $clientDuration = array_reduce($client['services'], function ($carry, $service) {
                return $carry + $service['duration'];
            }, 0);

            $estimatedTime->addMinutes($clientDuration);
        }

        return $estimatedTime;
    }
}
