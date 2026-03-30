<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DonorResource;
use App\Models\City;
use App\Models\Donor;
use App\Support\BloodGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DonorController extends Controller
{
    /**
     * Public donor directory (authenticated): lists enabled + available donors.
     * Optional filters: city_id, blood_group.
     */
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'blood_group' => ['nullable', 'string', Rule::in(BloodGroup::ALL)],
        ]);

        if (! empty($data['city_id'])) {
            $city = City::query()->where('id', $data['city_id'])->where('status', 'active')->first();
            if ($city === null) {
                return response()->json(['message' => 'Selected city is not available.'], 422);
            }
        }

        $me = $request->user();

        $rows = Donor::query()
            ->where('is_enabled', true)
            ->where('is_available', true)
            ->when(! empty($data['blood_group']), fn ($q) => $q->where('blood_group', $data['blood_group']))
            ->whereHas('user', function ($u) use ($data) {
                $u->where('is_blocked', false);
                if (! empty($data['city_id'])) {
                    $u->where('city_id', (int) $data['city_id']);
                }
            })
            ->with(['user.city'])
            ->orderByDesc('is_verified')
            ->latest('id')
            ->paginate(20);

        $out = collect($rows->items())->map(function (Donor $d) use ($me) {
            $u = $d->user;

            return [
                'id' => $d->id,
                'blood_group' => $d->blood_group,
                'is_verified' => (bool) $d->is_verified,
                'is_available' => (bool) $d->is_available,
                'user' => $u === null ? null : [
                    'id' => $u->id,
                    'name' => $u->name,
                    'mobile' => $u->mobile,
                    'city' => $u->city ? ['id' => $u->city->id, 'city_name' => $u->city->city_name] : null,
                    'area' => $u->area,
                ],
                'can_chat' => $u !== null && (int) $u->id !== (int) $me->id,
            ];
        })->values();

        return response()->json([
            'data' => $out,
            'meta' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'total' => $rows->total(),
            ],
        ]);
    }

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
            'age' => ['nullable', 'integer', 'min:16', 'max:120'],
            'last_donation_date' => 'nullable|date',
            'is_available' => 'sometimes|boolean',
        ]);

        $donor = Donor::query()->firstOrNew(['user_id' => $request->user()->id]);
        $donor->blood_group = $data['blood_group'];
        if (array_key_exists('age', $data)) {
            $donor->age = $data['age'];
        }
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
     * Donor feed: open emergency requests in same city.
     * Matching blood group is prioritized first, but not required.
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
            ->where('message', 'like', '[EMERGENCY]%')
            ->where('city_id', $user->city_id)
            ->where('user_id', '!=', $user->id)
            ->orderByRaw('CASE WHEN blood_group = ? THEN 0 ELSE 1 END', [$donor->blood_group])
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
