<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->with(['city', 'donor'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($nested) use ($q) {
                    $nested->where('name', 'like', "%{$q}%")
                        ->orWhere('mobile', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'query' => $q,
        ]);
    }

    public function toggleBlock(User $user): RedirectResponse
    {
        $user->is_blocked = ! $user->is_blocked;
        $user->save();

        return back()->with('status', $user->is_blocked ? 'User blocked.' : 'User unblocked.');
    }

    public function toggleDonorEnabled(User $user): RedirectResponse
    {
        if ($user->donor === null) {
            return back()->withErrors([
                'donor' => 'This user does not have a donor profile.',
            ]);
        }

        $user->donor->is_enabled = ! $user->donor->is_enabled;
        $user->donor->save();

        return back()->with(
            'status',
            $user->donor->is_enabled ? 'Donor profile enabled.' : 'Donor profile disabled.'
        );
    }
}

