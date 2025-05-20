<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import de tes controllers
use App\Http\Controllers\Salon\FindAllSalonsController;
use App\Http\Controllers\Salon\FindSalonByIdController;
use App\Http\Controllers\Salon\UpdateSalonController;
use App\Http\Controllers\SubscriptionController;

// Routes nécessitant l'authentification via Sanctum
Route::middleware(['auth:sanctum'])->group(function () {

    // Route déjà existante : /api/user
    Route::get('/user', function (Request $request) {
        return $request->user()->load('salon');
    });



    Route::get('/salon/{salonId}', FindSalonByIdController::class)
        ->name('salon.find-by-id');




    Route::get('/salons', FindAllSalonsController::class)
        ->name('salons.find-all');

    Route::post('/salon/{salonId}/update', UpdateSalonController::class)
        ->name('salons.update');

    // Routes d'abonnement
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('/cancel-subscription', [SubscriptionController::class, 'cancel']);
    Route::get('/subscription/plans', [SubscriptionController::class, 'getPlans']);
});
