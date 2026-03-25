<?php

namespace App\Providers;

use App\Jobs\NotifyMatchingDonorsOfBloodRequestJob;
use App\Models\BloodRequest;
use App\Services\FcmService;
use App\Contracts\FirebaseIdTokenVerifier;
use App\Services\KreaitFirebaseIdTokenVerifier;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FcmService::class, fn () => new FcmService);
        $this->app->singleton(FirebaseIdTokenVerifier::class, fn () => new KreaitFirebaseIdTokenVerifier);
    }

    public function boot(): void
    {
        RedirectIfAuthenticated::redirectUsing(fn () => route('admin.dashboard'));

        BloodRequest::created(function (BloodRequest $request) {
            // Prefer queue dispatch; fallback to sync if queue is sync or dispatch fails.
            try {
                if (config('queue.default') === 'sync') {
                    NotifyMatchingDonorsOfBloodRequestJob::dispatchSync($request->id);

                    return;
                }

                NotifyMatchingDonorsOfBloodRequestJob::dispatch($request->id)->afterCommit();
            } catch (Throwable $e) {
                Log::warning('FCM: donor notify dispatch failed, running sync fallback.', [
                    'blood_request_id' => $request->id,
                    'error' => $e->getMessage(),
                ]);
                try {
                    NotifyMatchingDonorsOfBloodRequestJob::dispatchSync($request->id);
                } catch (Throwable $e2) {
                    Log::error('FCM: donor notify sync fallback failed.', [
                        'blood_request_id' => $request->id,
                        'error' => $e2->getMessage(),
                    ]);
                }
            }
        });
    }
}
