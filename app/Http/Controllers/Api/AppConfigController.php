<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\RequestResponse;
use App\Models\AppSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AppConfigController extends Controller
{
    public function bootstrap(): JsonResponse
    {
        $payload = Cache::remember('api_bootstrap_v1', 60, function () {
            $base = AppSetting::current()->toBootstrapPayload();
            $base['impact'] = [
                'lives_saved' => Donation::query()->where('status', 'approved')->count(),
                'active_donors' => Donor::query()->where('is_enabled', true)->where('is_available', true)->count(),
                'emergency_responses_week' => RequestResponse::query()
                    ->where('status', 'interested')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count(),
            ];

            return $base;
        });

        return response()->json($payload);
    }
}
