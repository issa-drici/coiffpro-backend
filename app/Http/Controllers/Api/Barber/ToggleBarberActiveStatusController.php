<?php

namespace App\Http\Controllers\Api\Barber;

use App\Domain\UseCases\Barber\ToggleBarberActiveStatusUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ToggleBarberActiveStatusController extends Controller
{
    public function __construct(
        private readonly ToggleBarberActiveStatusUseCase $useCase
    ) {}

    public function __invoke(Request $request, string $barberId): JsonResponse
    {
        try {
            $barber = $this->useCase->execute($barberId);

            return response()->json([
                'message' => $barber->is_active
                    ? 'Barber activé avec succès'
                    : 'Barber désactivé avec succès',
                'data' => [
                    'id' => $barber->id,
                    'is_active' => $barber->is_active,
                    'is_active_changed_at' => $barber->is_active_changed_at,
                    'user' => $barber->user ? [
                        'id' => $barber->user->id,
                        'firstName' => $barber->user->firstName,
                        'lastName' => $barber->user->lastName,
                        'email' => $barber->user->email,
                    ] : null,
                ]
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la modification du statut'
            ], 500);
        }
    }
}
