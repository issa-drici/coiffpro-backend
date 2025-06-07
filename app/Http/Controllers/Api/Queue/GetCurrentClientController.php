<?php

namespace App\Http\Controllers\Api\Queue;

use App\Domain\UseCases\Queue\GetCurrentClientUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetCurrentClientController extends Controller
{
    public function __construct(
        private readonly GetCurrentClientUseCase $useCase
    ) {}

    public function __invoke(string $salonId): JsonResponse
    {
        try {
            $result = $this->useCase->execute($salonId);
            return response()->json($result);
        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération du client en cours.'
            ], 500);
        }
    }
}
