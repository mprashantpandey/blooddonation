<?php

use App\Http\Controllers\Api\AppConfigController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BloodRequestController;
use App\Http\Controllers\Api\V1\BloodRequestResponseController;
use App\Http\Controllers\Api\V1\CityController;
use App\Http\Controllers\Api\V1\CitySliderController;
use App\Http\Controllers\Api\V1\DonorController;
use App\Http\Controllers\Api\V1\DonationController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\WalletController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return [
        'status' => 'ok',
        'app' => config('app.name'),
    ];
});

Route::prefix('v1')->group(function () {
    Route::get('/bootstrap', [AppConfigController::class, 'bootstrap'])
        ->middleware('throttle:120,1');

    Route::get('/cities', [CityController::class, 'index'])
        ->middleware('throttle:60,1');
    Route::get('/city-sliders', [CitySliderController::class, 'index'])
        ->middleware('throttle:60,1');

    Route::post('/auth/sync', [AuthController::class, 'sync'])
        ->middleware('throttle:30,1');

    Route::middleware(['auth:sanctum', 'not.blocked'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::patch('/me', [AuthController::class, 'updateProfile']);
        Route::patch('/me/fcm-token', [AuthController::class, 'updateFcmToken']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::get('/donor/me', [DonorController::class, 'show']);
        Route::post('/donor', [DonorController::class, 'store']);
        Route::get('/donor/feed', [DonorController::class, 'feed']);

        Route::get('/blood-requests/mine', [BloodRequestController::class, 'mine']);
        Route::post('/blood-requests', [BloodRequestController::class, 'store']);
        Route::get('/blood-requests/{bloodRequest}', [BloodRequestController::class, 'show']);
        Route::get('/blood-requests/{bloodRequest}/interested-donors', [BloodRequestController::class, 'interestedDonors']);
        Route::post('/blood-requests/{bloodRequest}/respond', [BloodRequestResponseController::class, 'store']);

        Route::get('/donations/mine', [DonationController::class, 'mine']);
        Route::post('/donations', [DonationController::class, 'store']);
        Route::post('/donations/{donation}/proof', [DonationController::class, 'uploadProof']);

        Route::get('/wallet', [WalletController::class, 'summary']);
        Route::get('/wallet/entries', [WalletController::class, 'entries']);
        Route::get('/badges', [WalletController::class, 'badges']);

        Route::get('/messages/threads', [MessageController::class, 'threads']);
        Route::get('/messages/with/{user}', [MessageController::class, 'withUser']);
        Route::post('/messages/with/{user}', [MessageController::class, 'send']);
    });
});
