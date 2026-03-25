<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Donor;
use App\Models\User;
use App\Models\UserFcmToken;
use App\Services\FcmService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        return view('admin.notifications.index', [
            'cities' => City::query()->orderBy('city_name')->get(),
        ]);
    }

    public function send(Request $request, FcmService $fcm): RedirectResponse
    {
        if (! $fcm->isConfigured()) {
            return back()->withErrors(['fcm' => 'FCM is not configured. Add a server service account under Settings → Firebase.']);
        }

        $validated = $request->validate([
            'audience' => ['required', 'string', 'in:all_users,donors'],
            'title' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:500'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'blood_group' => ['nullable', 'string', 'max:8'],
            'only_available_donors' => ['nullable', 'boolean'],
        ]);

        $onlyAvailable = $request->boolean('only_available_donors');

        $tokens = [];
        if ($validated['audience'] === 'donors') {
            $q = Donor::query()
                ->where('is_enabled', true)
                ->when($onlyAvailable, fn ($qq) => $qq->where('is_available', true))
                ->when(! empty($validated['blood_group']), fn ($qq) => $qq->where('blood_group', $validated['blood_group']))
                ->whereHas('user', function ($u) use ($validated) {
                    $u->where('is_blocked', false);
                    if (! empty($validated['city_id'])) {
                        $u->where('city_id', (int) $validated['city_id']);
                    }
                })
                ->with(['user', 'user.fcmTokens']);

            foreach ($q->get() as $donor) {
                $user = $donor->user;
                if ($user === null) {
                    continue;
                }
                $userTokens = $user->fcmTokens->pluck('token')->map(fn ($t) => trim((string) $t))->filter()->all();
                if ($userTokens !== []) {
                    $tokens = array_merge($tokens, $userTokens);
                    continue;
                }
                $legacy = trim((string) ($user->fcm_token ?? ''));
                if ($legacy !== '') {
                    $tokens[] = $legacy;
                }
            }
        } else {
            $q = User::query()
                ->where('is_blocked', false)
                ->when(! empty($validated['city_id']), fn ($qq) => $qq->where('city_id', (int) $validated['city_id']))
                ->with('fcmTokens');

            foreach ($q->get() as $user) {
                $userTokens = $user->fcmTokens->pluck('token')->map(fn ($t) => trim((string) $t))->filter()->all();
                if ($userTokens !== []) {
                    $tokens = array_merge($tokens, $userTokens);
                    continue;
                }
                $legacy = trim((string) ($user->fcm_token ?? ''));
                if ($legacy !== '') {
                    $tokens[] = $legacy;
                }
            }
        }

        $tokens = array_values(array_unique($tokens));
        if ($tokens === []) {
            return back()->withErrors(['tokens' => 'No matching recipients (no FCM tokens found for selected filters).']);
        }

        $data = [
            'type' => 'admin_broadcast',
            'audience' => $validated['audience'],
        ];
        $result = $fcm->sendToTokens($tokens, $validated['title'], $validated['body'], $data);

        return back()->with('status', "Notification queued. Sent: {$result['sent']}, failed: {$result['failed']}.");
    }
}

