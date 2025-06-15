<?php

namespace App\Http\Controllers\Api\Queue;

use App\Domain\UseCases\Queue\MoveToNextQueueClientUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class MoveToNextQueueClientController extends Controller
{
    public function __construct(
        private readonly MoveToNextQueueClientUseCase $useCase
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
                'message' => 'Une erreur est survenue lors du passage au client suivant.'
            ], 500);
        }
    }
}
