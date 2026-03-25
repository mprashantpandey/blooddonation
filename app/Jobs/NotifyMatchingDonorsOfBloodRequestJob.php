<?php

namespace App\Jobs;

use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\Notification;
use App\Models\UserFcmToken;
use App\Services\FcmService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class NotifyMatchingDonorsOfBloodRequestJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $bloodRequestId) {}

    public function handle(FcmService $fcm): void
    {
        if (! $fcm->isConfigured()) {
            Log::info('FCM: skipping donor notifications (service account not configured).');

            return;
        }

        $request = BloodRequest::query()
            ->with('city')
            ->find($this->bloodRequestId);

        if ($request === null) {
            return;
        }

        if ($request->city === null || $request->city->status !== 'active') {
            Log::info('FCM: skipping notifications (inactive or missing city).', [
                'blood_request_id' => $this->bloodRequestId,
            ]);

            return;
        }

        $donors = Donor::query()
            ->where('blood_group', $request->blood_group)
            ->where('is_available', true)
            ->where('is_enabled', true)
            ->whereHas('user', function ($q) use ($request) {
                $q->where('city_id', $request->city_id)
                    ->where('is_blocked', false);
            })
            ->with(['user', 'user.fcmTokens'])
            ->get();

        $tokens = [];
        foreach ($donors as $donor) {
            $user = $donor->user;
            if ($user === null) {
                continue;
            }
            if ((int) $user->id === (int) $request->user_id) {
                continue;
            }
            $userTokens = $user->fcmTokens->pluck('token')->map(fn ($t) => trim((string) $t))->filter()->all();
            if ($userTokens !== []) {
                $tokens = array_merge($tokens, $userTokens);
                continue;
            }
            // legacy fallback
            $legacy = trim((string) $user->fcm_token);
            if ($legacy !== '') {
                $tokens[] = $legacy;
            }
        }

        $tokens = array_values(array_unique($tokens));

        if ($tokens === []) {
            Log::info('FCM: no matching donor tokens for blood request.', [
                'blood_request_id' => $request->id,
            ]);

            return;
        }

        $cityName = $request->city->city_name ?? 'your area';
        $title = '🚨 Blood needed in '.$cityName;
        $body = 'Tap to view the request.';

        $data = [
            'type' => 'blood_request',
            'request_id' => (string) $request->id,
            'city_id' => (string) $request->city_id,
        ];

        // Persist notifications for recipients (sync across devices).
        foreach ($donors as $donor) {
            $user = $donor->user;
            if ($user === null) {
                continue;
            }
            if ((int) $user->id === (int) $request->user_id) {
                continue;
            }
            Notification::query()->create([
                'user_id' => (int) $user->id,
                'type' => 'blood_request',
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'sent_at' => now(),
            ]);
        }

        $result = $fcm->sendToTokens($tokens, $title, $body, $data);

        Log::info('FCM: blood request donor notifications.', [
            'blood_request_id' => $request->id,
            'tokens' => count($tokens),
            'sent' => $result['sent'],
            'failed' => $result['failed'],
        ]);
    }
}
