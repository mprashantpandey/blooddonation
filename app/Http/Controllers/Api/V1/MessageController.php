<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\SendMessagePushJob;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function threads(Request $request): JsonResponse
    {
        $me = $request->user();

        $latest = Message::query()
            ->where(function ($q) use ($me) {
                $q->where('sender_id', $me->id)->orWhere('receiver_id', $me->id);
            })
            ->orderByDesc('sent_at')
            ->limit(300)
            ->get();

        $threads = [];
        foreach ($latest as $m) {
            $otherId = (int) ($m->sender_id === $me->id ? $m->receiver_id : $m->sender_id);
            if (! isset($threads[$otherId])) {
                $threads[$otherId] = $m;
            }
        }

        $others = User::query()->whereIn('id', array_keys($threads))->get()->keyBy('id');

        $data = collect($threads)->map(function (Message $m, int $otherId) use ($others) {
            $other = $others->get($otherId);

            return [
                'user' => $other ? ['id' => $other->id, 'name' => $other->name] : ['id' => $otherId, 'name' => null],
                'last_message' => [
                    'id' => $m->id,
                    'message' => $m->message,
                    'sent_at' => $m->sent_at?->toIso8601String(),
                    'from_me' => (int) $m->sender_id === (int) auth()->id(),
                ],
            ];
        })->values();

        return response()->json(['data' => $data]);
    }

    public function withUser(Request $request, User $user): JsonResponse
    {
        $me = $request->user();

        $rows = Message::query()
            ->where(function ($q) use ($me, $user) {
                $q->where('sender_id', $me->id)->where('receiver_id', $user->id);
            })
            ->orWhere(function ($q) use ($me, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $me->id);
            })
            ->orderByDesc('sent_at')
            ->paginate(50);

        $data = collect($rows->items())->map(function (Message $m) use ($me) {
            return [
                'id' => $m->id,
                'sender_id' => $m->sender_id,
                'receiver_id' => $m->receiver_id,
                'message' => $m->message,
                'attachment_url' => $m->attachment_url,
                'attachment_mime' => $m->attachment_mime,
                'sent_at' => $m->sent_at?->toIso8601String(),
                'from_me' => (int) $m->sender_id === (int) $me->id,
            ];
        })->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'total' => $rows->total(),
            ],
        ]);
    }

    public function send(Request $request, User $user): JsonResponse
    {
        $me = $request->user();
        if ((int) $me->id === (int) $user->id) {
            return response()->json(['message' => 'Cannot message yourself.'], 422);
        }

        $data = $request->validate([
            'message' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
        ]);

        $text = trim((string) ($data['message'] ?? ''));
        $file = $request->file('attachment');
        if ($text === '' && $file === null) {
            return response()->json(['message' => 'Message or attachment is required.'], 422);
        }

        $attachmentUrl = null;
        $attachmentMime = null;
        if ($file !== null) {
            $path = $file->store('message-attachments', 'public');
            $attachmentUrl = Storage::disk('public')->url($path);
            $attachmentMime = $file->getClientMimeType();
        }

        $msg = Message::query()->create([
            'sender_id' => $me->id,
            'receiver_id' => $user->id,
            'message' => $text === '' ? null : $text,
            'attachment_url' => $attachmentUrl,
            'attachment_mime' => $attachmentMime,
            'sent_at' => now(),
        ]);

        // Push notify receiver devices. We run this sync to avoid relying on a queue worker.
        // If FCM isn't configured, the job will no-op.
        SendMessagePushJob::dispatchSync((int) $msg->id);

        return response()->json([
            'data' => [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'receiver_id' => $msg->receiver_id,
                'message' => $msg->message,
                'attachment_url' => $msg->attachment_url,
                'attachment_mime' => $msg->attachment_mime,
                'sent_at' => $msg->sent_at?->toIso8601String(),
            ],
        ], 201);
    }
}

