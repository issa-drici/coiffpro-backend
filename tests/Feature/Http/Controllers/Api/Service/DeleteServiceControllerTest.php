<?php

use App\Domain\UseCases\Service\DeleteServiceUseCase;
use App\Infrastructure\Models\ServiceModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('delete service endpoint returns 404 when service not found', function () {
    // Arrange
    $serviceId = '123e4567-e89b-12d3-a456-426614174000';

    // Act
    $response = $this->deleteJson("/api/services/{$serviceId}");

    // Assert
    $response->assertStatus(404)
        ->assertJson([
            'message' => 'Service non trouvé'
        ]);
});

test('delete service endpoint returns success when service is deleted', function () {
    // Arrange
    $service = ServiceModel::factory()->create();

    // Act
    $response = $this->deleteJson("/api/services/{$service->id}");

    // Assert
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Service supprimé avec succès'
        ]);

    $this->assertDatabaseMissing('services', [
        'id' => $service->id
    ]);
});

test('delete service endpoint returns 400 when domain exception occurs', function () {
    // Arrange
    $service = ServiceModel::factory()->create();

    // Mock le use case pour simuler une erreur de domaine
    $this->mock(DeleteServiceUseCase::class, function ($mock) use ($service) {
        $mock->shouldReceive('execute')
            ->once()
            ->with($service->id)
            ->andThrow(new \DomainException('Erreur lors de la suppression'));
    });

    // Act
    $response = $this->deleteJson("/api/services/{$service->id}");

    // Assert
    $response->assertStatus(400)
        ->assertJson([
            'message' => 'Erreur lors de la suppression'
        ]);
});
