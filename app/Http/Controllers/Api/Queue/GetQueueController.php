<?php

namespace App\Http\Controllers\Api\Queue;

use App\Domain\UseCases\Queue\GetQueueUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class GetQueueController extends Controller
{
    public function __construct(
        private readonly GetQueueUseCase $useCase
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
                'message' => 'Une erreur est survenue lors de la récupération de la file d\'attente.'
            ], 500);
        }
    }
}
