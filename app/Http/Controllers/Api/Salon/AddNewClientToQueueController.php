<?php

namespace App\Http\Controllers\Api\Salon;

use App\Domain\UseCases\Queue\AddNewClientToQueueUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddNewClientToQueueController extends Controller
{
    public function __construct(
        private readonly AddNewClientToQueueUseCase $useCase
    ) {}

    public function __invoke(Request $request, string $salonId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phoneNumber' => 'required|string|max:20',
                'services' => 'required|array',
                'services.*' => 'required|string|uuid',
                'notes' => 'nullable|string|max:500'
            ]);

            // Ajouter le salon_id aux données validées
            $validated['salon_id'] = $salonId;

            $result = $this->useCase->execute($validated);

            // Envoyer un SMS de confirmation
            $this->sendConfirmationSMS($validated, $result['data']);

            return response()->json([
                'message' => $result['message'],
                'data' => $result['data']
            ], 201);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    private function sendConfirmationSMS(array $clientData, $queueClient): void
    {
        try {
            // Préparer le message
            $services = $queueClient['services']->pluck('name')->implode(', ');
            $message = "Bonjour {$clientData['firstName']}, vous êtes inscrit(e) dans notre file d'attente pour : {$services}. Votre numéro de ticket est : {$queueClient['ticket_number']}. Nous vous contacterons quand ce sera votre tour.";

            // Formater le numéro de téléphone avec le préfixe +33
            $phoneNumber = $clientData['phoneNumber'];
            if (!str_starts_with($phoneNumber, '+33')) {
                // Enlever le 0 au début si présent et ajouter +33
                $phoneNumber = '+33' . ltrim($phoneNumber, '0');
            }

            // Envoyer le SMS via l'API
            $response = Http::withBasicAuth('ZY89ZO', 'fottqo1upxcoap')
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post('https://api.sms-gate.app/3rdparty/v1/message', [
                    'message' => $message,
                    'phoneNumbers' => [$phoneNumber]
                ]);

            // Log de la réponse complète
            Log::info('Réponse API SMS', [
                'status_code' => $response->status(),
                'response_body' => $response->body(),
                'response_headers' => $response->headers(),
                'successful' => $response->successful()
            ]);

            // Log de la réponse pour debug (optionnel)
            if (!$response->successful()) {
                Log::warning('Échec envoi SMS confirmation', [
                    'phone_number' => $phoneNumber,
                    'status_code' => $response->status(),
                    'response' => $response->body()
                ]);
            } else {
                Log::info('SMS envoyé avec succès', [
                    'phone_number' => $phoneNumber,
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            // Log de l'erreur mais ne pas faire échouer l'inscription
            Log::error('Erreur envoi SMS confirmation', [
                'phone_number' => $phoneNumber ?? $clientData['phoneNumber'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
