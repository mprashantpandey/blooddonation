<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\City;
use App\Models\Donor;
use App\Models\User;
use App\Models\BloodRequest;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'counts' => [
                'users' => User::query()->count(),
                'donors' => Donor::query()->count(),
                'requests' => BloodRequest::query()->count(),
                'cities' => City::query()->count(),
            ],
            'settings' => AppSetting::current(),
        ]);
    }
}
