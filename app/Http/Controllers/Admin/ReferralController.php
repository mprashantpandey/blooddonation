<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReferralController extends Controller
{
    public function index(Request $request): View
    {
        $rows = Referral::query()
            ->with(['referrer', 'newUser'])
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.referrals.index', [
            'referrals' => $rows,
        ]);
    }
}

