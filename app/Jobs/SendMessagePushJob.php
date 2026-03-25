<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\UserFcmToken;
use App\Services\FcmService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SendMessagePushJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public int $messageId) {}

    public function handle(FcmService $fcm): void
    {
        if (! $fcm->isConfigured()) {
            return;
        }

        $msg = Message::query()
            ->with(['sender', 'receiver'])
            ->find($this->messageId);

        if ($msg === null || $msg->receiver === null) {
            return;
        }

        $receiverId = (int) $msg->receiver_id;
        $tokens = UserFcmToken::query()
            ->where('user_id', $receiverId)
            ->pluck('token')
            ->map(fn ($t) => trim((string) $t))
            ->filter(fn ($t) => $t !== '')
            ->unique()
            ->values()
            ->all();

        // fallback to legacy column
        if ($tokens === []) {
            $legacy = trim((string) ($msg->receiver->fcm_token ?? ''));
            if ($legacy !== '') {
                $tokens = [$legacy];
            }
        }

        if ($tokens === []) {
            return;
        }

        $senderName = (string) ($msg->sender?->name ?? 'New message');
        $title = $senderName;
        $body = Str::limit((string) $msg->message, 140, '…');

        $data = [
            'type' => 'message',
            'sender_id' => (string) $msg->sender_id,
            'message_id' => (string) $msg->id,
        ];

        $result = $fcm->sendToTokens($tokens, $title, $body, $data);
        Log::info('FCM: message push', [
            'message_id' => $msg->id,
            'receiver_id' => $receiverId,
            'tokens' => count($tokens),
            'sent' => $result['sent'],
            'failed' => $result['failed'],
        ]);
    }
}

