<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AppConfigController extends Controller
{
    public function bootstrap(): JsonResponse
    {
        $payload = Cache::remember('api_bootstrap_v1', 60, function () {
            return AppSetting::current()->toBootstrapPayload();
        });

        return response()->json($payload);
    }
}
