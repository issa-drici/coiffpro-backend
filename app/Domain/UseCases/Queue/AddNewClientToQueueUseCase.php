<?php

namespace App\Domain\UseCases\Queue;

use App\Domain\Repositories\Interfaces\ClientRepositoryInterface;
use App\Domain\Repositories\Interfaces\QueueClientRepositoryInterface;
use App\Domain\Repositories\Interfaces\ServiceRepositoryInterface;
use App\Infrastructure\Models\QueueClientModel;
use App\Infrastructure\Models\ClientModel;
use Carbon\Carbon;

class AddNewClientToQueueUseCase
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly QueueClientRepositoryInterface $queueClientRepository,
        private readonly ServiceRepositoryInterface $serviceRepository
    ) {}

    private function generateTicketNumber(string $salonId): int
    {
        $now = Carbon::now();

        // Opérations sur chaque partie
        $dayNumber = ($now->day * 37) + $now->day + 3;
        $monthNumber = ($now->month * 9);
        $yearNumber = ($now->year % 100) * 3 + $now->day * 2 + 7;

        // Formater avec des zéros devant si nécessaire
        // $day = str_pad($dayNumber, 2, '0', STR_PAD_LEFT);
        // $month = str_pad($monthNumber, 2, '0', STR_PAD_LEFT);
        // $year = str_pad($yearNumber, 2, '0', STR_PAD_LEFT);

        // Concaténer les résultats
        $baseNumber = (int) ($yearNumber . $monthNumber . $dayNumber);

        // Récupérer le dernier ticket du jour pour ce salon
        $lastTicket = $this->queueClientRepository->findLastTicketOfDay($now, $salonId);

        // Si aucun ticket n'existe pour aujourd'hui, commencer à partir du numéro de base
        if (!$lastTicket) {
            return $baseNumber;
        }

        // Sinon, décrémenter le dernier numéro
        return $lastTicket->ticket_number - 1;
    }

    public function execute(array $data): array
    {
        // Validation des données requises
        if (!isset($data['salon_id']) || !isset($data['services']) || !isset($data['firstName']) || !isset($data['lastName']) || !isset($data['phoneNumber'])) {
            throw new \DomainException('Les champs salon_id, services, firstName, lastName et phoneNumber sont obligatoires.');
        }

        // Vérifier que tous les services existent et appartiennent au salon
        $totalAmount = 0;
        foreach ($data['services'] as $serviceId) {
            $service = $this->serviceRepository->findById($serviceId);
            if (!$service) {
                throw new \DomainException("Le service avec l'ID $serviceId n'existe pas.");
            }
            if ($service->salon_id !== $data['salon_id']) {
                throw new \DomainException("Le service avec l'ID $serviceId n'appartient pas à ce salon.");
            }
            $totalAmount += $service->price;
        }

        // Rechercher un client existant par téléphone et prénom (insensible à la casse)
        $client = $this->clientRepository->findByPhoneAndFirstName(
            $data['phoneNumber'],
            strtolower($data['firstName']),
            $data['salon_id']
        );

        if ($client) {
            // Vérifier si le client est déjà dans une file d'attente active
            $existingQueueClients = $this->queueClientRepository->findAllByClient($client->id);
            foreach ($existingQueueClients as $queueClient) {
                if (in_array($queueClient->status, ['waiting', 'in_progress'])) {
                    // Retourner les informations du client existant en file d'attente
                    return [
                        'success' => true,
                        'message' => 'Client déjà en file d\'attente',
                        'data' => $queueClient
                    ];
                }
            }
        } else {
            // Créer un nouveau client
            $client = $this->clientRepository->create([
                'firstName' => $data['firstName'],
                'lastName' => $data['lastName'],
                'email' => $data['email'] ?? null,
                'phoneNumber' => $data['phoneNumber'],
                'salon_id' => $data['salon_id']
            ]);
        }

        // Générer le numéro de ticket pour ce salon
        $ticketNumber = $this->generateTicketNumber($data['salon_id']);

        // Créer l'entrée dans la file d'attente
        $queueClient = $this->queueClientRepository->create([
            'client_id' => $client->id,
            'salon_id' => $data['salon_id'],
            'status' => 'waiting',
            'amountToPay' => $totalAmount,
            'notes' => $data['notes'] ?? null,
            'ticket_number' => $ticketNumber
        ]);

        // Attacher les services
        $this->queueClientRepository->attachServices($queueClient->id, $data['services']);

        return [
            'success' => true,
            'message' => 'Client ajouté à la file d\'attente avec succès',
            'data' => $this->queueClientRepository->findById($queueClient->id)
        ];
    }
}
