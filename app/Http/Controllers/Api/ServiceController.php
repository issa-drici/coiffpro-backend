<?php

namespace App\Http\Controllers\Api;

use App\Domain\UseCases\Service\CreateServiceUseCase;
use App\Domain\UseCases\Service\GetSalonServicesUseCase;
use App\Domain\UseCases\Service\GetServicesByCategoryUseCase;
use App\Domain\UseCases\Service\UpdateServiceUseCase;
use App\Domain\UseCases\Service\DeleteServiceUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(
        private readonly CreateServiceUseCase $createServiceUseCase,
        private readonly GetSalonServicesUseCase $getSalonServicesUseCase,
        private readonly GetServicesByCategoryUseCase $getServicesByCategoryUseCase,
        private readonly UpdateServiceUseCase $updateServiceUseCase,
        private readonly DeleteServiceUseCase $deleteServiceUseCase
    ) {}

    /**
     * Créer un nouveau service
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'salon_id' => 'required|string|uuid',
                'name' => 'required|string|max:255',
                'category' => 'required|string|max:50',
                'duration' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:500'
            ]);

            $service = $this->createServiceUseCase->execute($validated);

            return response()->json([
                'message' => 'Service créé avec succès',
                'data' => $service
            ], 201);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Récupérer tous les services d'un salon
     */
    public function index(string $salonId): JsonResponse
    {
        try {
            $services = $this->getSalonServicesUseCase->execute($salonId);

            return response()->json([
                'data' => $services
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Récupérer les services par catégorie
     */
    public function getByCategory(string $salonId, string $category): JsonResponse
    {
        try {
            $services = $this->getServicesByCategoryUseCase->execute($category, $salonId);

            return response()->json([
                'data' => $services
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Mettre à jour un service
     */
    public function update(Request $request, string $serviceId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'category' => 'sometimes|required|string|max:50',
                'duration' => 'sometimes|required|integer|min:1',
                'price' => 'sometimes|required|numeric|min:0',
                'description' => 'nullable|string|max:500'
            ]);

            $service = $this->updateServiceUseCase->execute($serviceId, $validated);

            return response()->json([
                'message' => 'Service mis à jour avec succès',
                'data' => $service
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Supprimer un service
     */
    public function destroy(string $serviceId): JsonResponse
    {
        try {
            $this->deleteServiceUseCase->execute($serviceId);

            return response()->json([
                'message' => 'Service supprimé avec succès'
            ]);
        } catch (\DomainException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
