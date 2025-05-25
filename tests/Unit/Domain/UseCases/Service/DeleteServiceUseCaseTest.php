<?php

use App\Domain\UseCases\Service\DeleteServiceUseCase;
use App\Domain\Repositories\Interfaces\ServiceRepositoryInterface;
use Mockery;

test('delete service use case returns true when service is deleted', function () {
    // Arrange
    $serviceId = '123e4567-e89b-12d3-a456-426614174000';
    $serviceRepository = Mockery::mock(ServiceRepositoryInterface::class);
    $serviceRepository->shouldReceive('delete')
        ->once()
        ->with($serviceId)
        ->andReturn(true);

    $useCase = new DeleteServiceUseCase($serviceRepository);

    // Act
    $result = $useCase->execute($serviceId);

    // Assert
    expect($result)->toBeTrue();
});

test('delete service use case returns false when service is not found', function () {
    // Arrange
    $serviceId = '123e4567-e89b-12d3-a456-426614174000';
    $serviceRepository = Mockery::mock(ServiceRepositoryInterface::class);
    $serviceRepository->shouldReceive('delete')
        ->once()
        ->with($serviceId)
        ->andReturn(false);

    $useCase = new DeleteServiceUseCase($serviceRepository);

    // Act
    $result = $useCase->execute($serviceId);

    // Assert
    expect($result)->toBeFalse();
});

test('delete service use case throws domain exception when repository throws exception', function () {
    // Arrange
    $serviceId = '123e4567-e89b-12d3-a456-426614174000';
    $serviceRepository = Mockery::mock(ServiceRepositoryInterface::class);
    $serviceRepository->shouldReceive('delete')
        ->once()
        ->with($serviceId)
        ->andThrow(new \DomainException('Erreur lors de la suppression'));

    $useCase = new DeleteServiceUseCase($serviceRepository);

    // Act & Assert
    expect(fn () => $useCase->execute($serviceId))
        ->toThrow(\DomainException::class, 'Erreur lors de la suppression');
});
