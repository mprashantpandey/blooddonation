<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\SendMessagePushJob;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $msg = Message::query()->create([
            'sender_id' => $me->id,
            'receiver_id' => $user->id,
            'message' => $data['message'],
            'sent_at' => now(),
        ]);

        // Push notify receiver devices (async if queue is configured).
        dispatch(new SendMessagePushJob((int) $msg->id));

        return response()->json([
            'data' => [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'receiver_id' => $msg->receiver_id,
                'message' => $msg->message,
                'sent_at' => $msg->sent_at?->toIso8601String(),
            ],
        ], 201);
    }
}

