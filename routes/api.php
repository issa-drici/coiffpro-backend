<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import de tes controllers
use App\Http\Controllers\Salon\FindAllSalonsController;
use App\Http\Controllers\Salon\FindSalonByIdController;
use App\Http\Controllers\Salon\UpdateSalonController;
use App\Http\Controllers\SubscriptionController;

// Import des nouveaux controllers
use App\Http\Controllers\Api\Client\CreateClientController;
use App\Http\Controllers\Api\Client\GetClientController;
use App\Http\Controllers\Api\Client\UpdateClientController;
use App\Http\Controllers\Api\Client\DeleteClientController;

use App\Http\Controllers\Api\Service\CreateServiceController;
use App\Http\Controllers\Api\Service\GetServiceController;
use App\Http\Controllers\Api\Service\UpdateServiceController;
use App\Http\Controllers\Api\Service\DeleteServiceController;
use App\Http\Controllers\Api\Service\GetAllServicesController;

use App\Http\Controllers\Api\Queue\AddClientToQueueController;
use App\Http\Controllers\Api\Queue\GetWaitingClientsController;
use App\Http\Controllers\Api\Queue\GetCurrentClientController;
use App\Http\Controllers\Api\Queue\MoveToNextQueueClientController;
use App\Http\Controllers\Api\Queue\CancelQueueClientController;
use App\Http\Controllers\Api\Queue\GetAbsentClientsController;
use App\Http\Controllers\Api\Queue\GetEndedClientsController;
use App\Http\Controllers\Api\Queue\GetQueueClientController;
use App\Http\Controllers\QueueClientController;
use App\Http\Controllers\Api\Queue\UpdateQueueClientStatusController;
use App\Http\Controllers\Api\Queue\GetQueueHistoryController;
use App\Http\Controllers\Api\Queue\GetQueueController;

use App\Http\Controllers\Api\Salon\GetSalonServicesController;
use App\Http\Controllers\Api\Salon\GetEstimatedTimeController;
use App\Http\Controllers\Api\Salon\AddNewClientToQueueController;

use App\Http\Controllers\Api\Barber\ToggleBarberActiveStatusController;
use App\Http\Controllers\Api\GenerateQrCodePdfController;

// Routes nécessitant l'authentification via Sanctum
Route::middleware(['auth:sanctum'])->group(function () {

    // Route déjà existante : /api/user
    Route::get('/user', function (Request $request) {
        return $request->user()->load(['salon', 'barber']);
    });

    Route::get('/salons/{salonId}/estimated-time', GetEstimatedTimeController::class)
        ->name('salon.estimated-time');

    Route::get('/salons', FindAllSalonsController::class)
        ->name('salons.find-all');

    Route::post('/salon/{salonId}/update', UpdateSalonController::class)
        ->name('salons.update');

    // Routes d'abonnement
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('/cancel-subscription', [SubscriptionController::class, 'cancel']);
    Route::get('/subscription/plans', [SubscriptionController::class, 'getPlans']);

    // Routes pour les clients
    Route::prefix('clients')->group(function () {
        Route::post('/', CreateClientController::class)->name('clients.create');
        Route::get('/{clientId}', GetClientController::class)->name('clients.get');
        Route::put('/{clientId}', UpdateClientController::class)->name('clients.update');
        Route::delete('/{clientId}', DeleteClientController::class)->name('clients.delete');
    });

    // Routes pour les services
    Route::prefix('services')->group(function () {
        Route::get('/', GetAllServicesController::class)->name('services.index');
        Route::post('/', CreateServiceController::class)->name('services.create');
        Route::get('/{serviceId}', GetServiceController::class)->name('services.get');
        Route::put('/{serviceId}', UpdateServiceController::class)->name('services.update');
        Route::delete('/{serviceId}', DeleteServiceController::class)->name('services.delete');
    });

    // Routes pour les barbers
    Route::prefix('barbers')->group(function () {
        Route::patch('/{barberId}/toggle-status', ToggleBarberActiveStatusController::class)
            ->name('barbers.toggle-status');
    });

    // Routes pour générer un PDF avec QR code
    Route::post('/generate-qrcode-pdf', GenerateQrCodePdfController::class)
        ->name('generate.qrcode-pdf');

    // Routes pour la file d'attente
    Route::prefix('queue')->group(function () {
        Route::get('/waiting/{salonId}', GetWaitingClientsController::class)->name('queue.waiting');
        Route::get('/current/{salonId}', GetCurrentClientController::class)->name('queue.current');
        Route::get('/absent/{salonId}', GetAbsentClientsController::class)->name('queue.absent');
        Route::get('/ended/{salonId}', GetEndedClientsController::class)->name('queue.ended');
        Route::post('/next/{salonId}', MoveToNextQueueClientController::class)->name('queue.next');
        Route::post('/clients', AddClientToQueueController::class);
        Route::get('/history/{salonId}', GetQueueHistoryController::class)->name('queue.history');
    });

    // Routes pour la file d'attente
    Route::prefix('queue-client')->group(function () {
        Route::get('/{queueClientId}', GetQueueClientController::class);
        Route::delete('/{queueClientId}', CancelQueueClientController::class)->name('queue.cancel');
        Route::patch('/clients/{queueClientId}/status', UpdateQueueClientStatusController::class);
    });
});

Route::get('/queue-client/{queueClientId}', GetQueueClientController::class)->name('queue.client.get');

// Route::get('/queue/{salonId}', GetQueueController::class)->name('queue.list');

Route::post('/salons/{salonId}/queue', AddNewClientToQueueController::class)
    ->name('salon.queue.add');

Route::get('/salons/{salonId}/services', GetSalonServicesController::class)
    ->name('salon.services');

Route::get('/salon/{salonId}', FindSalonByIdController::class)
    ->name('salon.find-by-id');
