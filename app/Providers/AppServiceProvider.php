<?php

namespace App\Providers;

use App\Jobs\NotifyMatchingDonorsOfBloodRequestJob;
use App\Models\BloodRequest;
use App\Services\FcmService;
use App\Contracts\FirebaseIdTokenVerifier;
use App\Services\KreaitFirebaseIdTokenVerifier;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\ServiceProvider;

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
            NotifyMatchingDonorsOfBloodRequestJob::dispatch($request->id)->afterCommit();
        });
    }
}
