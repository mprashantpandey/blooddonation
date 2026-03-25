<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PointsController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->with(['city'])
            ->withSum('walletEntries as points_balance', 'points')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($nested) use ($q) {
                    $nested->where('name', 'like', "%{$q}%")
                        ->orWhere('mobile', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('points_balance')
            ->paginate(20)
            ->withQueryString();

        return view('admin.points.index', [
            'users' => $users,
            'query' => $q,
        ]);
    }

    public function adjust(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'points' => ['required', 'integer', 'min:-10000', 'max:10000'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $points = (int) $validated['points'];
        if ($points === 0) {
            return back()->withErrors(['points' => 'Points must be non-zero.']);
        }

        WalletEntry::query()->create([
            'user_id' => $user->id,
            'points' => $points,
            'type' => 'admin_adjustment',
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('status', 'Points adjusted.');
    }
}

