<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DonorResource;
use App\Models\Donor;
use App\Support\BloodGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DonorController extends Controller
{
    public function show(Request $request): JsonResponse|DonorResource
    {
        $donor = $request->user()->donor;
        if ($donor === null) {
            return response()->json(['message' => 'No donor profile yet.'], 404);
        }

        return new DonorResource($donor);
    }

    public function store(Request $request): DonorResource|JsonResponse
    {
        if ($request->user()->city_id === null) {
            return response()->json([
                'message' => 'Set your city in your profile before registering as a donor.',
            ], 422);
        }

        $data = $request->validate([
            'blood_group' => ['required', 'string', Rule::in(BloodGroup::ALL)],
            'last_donation_date' => 'nullable|date',
            'is_available' => 'sometimes|boolean',
        ]);

        $donor = Donor::query()->firstOrNew(['user_id' => $request->user()->id]);
        $donor->blood_group = $data['blood_group'];
        if (array_key_exists('last_donation_date', $data)) {
            $donor->last_donation_date = $data['last_donation_date'];
        }

        if (! $donor->exists) {
            $donor->is_available = $data['is_available'] ?? true;
            $donor->is_enabled = true;
            $donor->is_verified = false;
        } elseif (array_key_exists('is_available', $data)) {
            $donor->is_available = $data['is_available'];
        }

        $donor->save();

        return new DonorResource($donor);
    }

    /**
     * Donor feed: open requests in same city + matching blood group.
     */
    public function feed(Request $request): JsonResponse
    {
        $user = $request->user();
        $donor = $user->donor;
        if ($donor === null || ! $donor->is_enabled) {
            return response()->json(['message' => 'You need an active donor profile to view the feed.'], 422);
        }
        if ($user->city_id === null) {
            return response()->json(['message' => 'Set your city in your profile to view the feed.'], 422);
        }

        $rows = \App\Models\BloodRequest::query()
            ->with(['city', 'user'])
            ->where('status', 'open')
            ->where('city_id', $user->city_id)
            ->where('blood_group', $donor->blood_group)
            ->where('user_id', '!=', $user->id)
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => $rows->items(),
            'meta' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'total' => $rows->total(),
            ],
        ]);
    }
}
