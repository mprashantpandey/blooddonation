<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BloodRequestController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CitySliderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DonationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', fn () => redirect()->route('admin.dashboard'))->name('home');
        Route::get('dashboard', DashboardController::class)->name('dashboard');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('profile/password', [ProfileController::class, 'editPassword'])->name('profile.password');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

        Route::get('cities', [CityController::class, 'index'])->name('cities.index');
        Route::post('cities', [CityController::class, 'store'])->name('cities.store');
        Route::put('cities/{city}', [CityController::class, 'update'])->name('cities.update');
        Route::delete('cities/{city}', [CityController::class, 'destroy'])->name('cities.destroy');

        Route::get('city-sliders', [CitySliderController::class, 'index'])->name('city-sliders.index');
        Route::post('city-sliders', [CitySliderController::class, 'store'])->name('city-sliders.store');
        Route::put('city-sliders/{citySlider}', [CitySliderController::class, 'update'])->name('city-sliders.update');
        Route::delete('city-sliders/{citySlider}', [CitySliderController::class, 'destroy'])->name('city-sliders.destroy');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}/block', [UserController::class, 'toggleBlock'])->name('users.block.toggle');
        Route::patch('users/{user}/donor-enabled', [UserController::class, 'toggleDonorEnabled'])->name('users.donor.enabled.toggle');

        Route::get('requests', [BloodRequestController::class, 'index'])->name('requests.index');
        Route::patch('requests/{bloodRequest}/status', [BloodRequestController::class, 'updateStatus'])->name('requests.status.update');

        Route::get('donations', [DonationController::class, 'index'])->name('donations.index');
        Route::patch('donations/{donation}/approve', [DonationController::class, 'approve'])->name('donations.approve');
        Route::patch('donations/{donation}/reject', [DonationController::class, 'reject'])->name('donations.reject');

        Route::redirect('settings', 'settings/branding')->name('settings');

        Route::get('settings/branding', [SettingsController::class, 'editBranding'])->name('settings.branding');
        Route::put('settings/branding', [SettingsController::class, 'updateBranding'])->name('settings.branding.update');

        Route::get('settings/welcome', [SettingsController::class, 'editWelcome'])->name('settings.welcome');
        Route::put('settings/welcome', [SettingsController::class, 'updateWelcome'])->name('settings.welcome.update');

        Route::get('settings/features', [SettingsController::class, 'editFeatures'])->name('settings.features');
        Route::put('settings/features', [SettingsController::class, 'updateFeatures'])->name('settings.features.update');

        Route::get('settings/auth', [SettingsController::class, 'editAuth'])->name('settings.auth');
        Route::put('settings/auth', [SettingsController::class, 'updateAuth'])->name('settings.auth.update');

        Route::get('settings/firebase', [SettingsController::class, 'editFirebase'])->name('settings.firebase');
        Route::put('settings/firebase', [SettingsController::class, 'updateFirebase'])->name('settings.firebase.update');
        Route::put('settings/firebase/fcm-service', [SettingsController::class, 'updateFcmServiceAccount'])->name('settings.firebase.fcm.update');
        Route::put('settings/firebase/web', [SettingsController::class, 'updateFirebaseWeb'])->name('settings.firebase.web.update');
    });
});
