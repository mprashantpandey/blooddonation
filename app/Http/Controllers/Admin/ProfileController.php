<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('admin.profile.edit', [
            'admin' => Auth::guard('admin')->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var \App\Models\Admin|null $admin */
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('admins', 'email')->ignore($admin?->id),
            ],
        ]);

        $admin->fill($data)->save();

        return back()->with('status', 'Profile updated.');
    }

    public function editPassword(): View
    {
        return view('admin.profile.password', [
            'admin' => Auth::guard('admin')->user(),
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var \App\Models\Admin|null $admin */
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            abort(403);
        }

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($data['current_password'], $admin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $admin->password = $data['password'];
        $admin->save();

        return back()->with('status', 'Password updated.');
    }
}

