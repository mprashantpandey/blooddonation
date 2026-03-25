<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\WalletEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $balance = (int) WalletEntry::query()->where('user_id', $user->id)->sum('points');

        return response()->json([
            'data' => [
                'points_balance' => $balance,
            ],
        ]);
    }

    public function entries(Request $request): JsonResponse
    {
        $user = $request->user();
        $rows = WalletEntry::query()
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(30);

        $data = collect($rows->items())->map(fn (WalletEntry $e) => [
            'id' => $e->id,
            'points' => (int) $e->points,
            'type' => $e->type,
            'description' => $e->description,
            'created_at' => $e->created_at?->toIso8601String(),
        ])->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'total' => $rows->total(),
            ],
        ]);
    }

    public function badges(Request $request): JsonResponse
    {
        $user = $request->user();
        $rows = Badge::query()->where('user_id', $user->id)->orderBy('badge_name')->get();

        return response()->json([
            'data' => $rows->map(fn (Badge $b) => [
                'id' => $b->id,
                'badge_name' => $b->badge_name,
            ])->values(),
        ]);
    }
}

