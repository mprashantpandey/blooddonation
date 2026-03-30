<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BloodRequestResource;
use App\Models\BloodRequest;
use App\Models\City;
use App\Models\User;
use App\Support\BloodGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class BloodRequestController extends Controller
{
    public function mine(Request $request): AnonymousResourceCollection
    {
        $requests = $request->user()
            ->bloodRequests()
            ->with('city')
            ->latest()
            ->paginate(20);

        return BloodRequestResource::collection($requests);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'patient_name' => 'required|string|max:255',
            'blood_group' => ['required', 'string', Rule::in(BloodGroup::ALL)],
            'city_id' => 'required|integer|exists:cities,id',
            'hospital' => 'required|string|max:255',
            'message' => 'nullable|string|max:5000',
        ]);

        $city = City::query()->where('id', $data['city_id'])->where('status', 'active')->first();
        if ($city === null) {
            return response()->json(['message' => 'Selected city is not available.'], 422);
        }

        BloodRequest::query()
            ->where('user_id', $request->user()->id)
            ->where('status', 'open')
            ->update(['status' => 'closed']);

        $incomingMessage = trim((string) ($data['message'] ?? ''));
        $message = '[EMERGENCY]';
        if ($incomingMessage !== '') {
            $cleanIncoming = preg_replace('/^\[[A-Z_]+\]\s*/', '', $incomingMessage) ?? $incomingMessage;
            $message = '[EMERGENCY] '.$cleanIncoming;
        }

        $bloodRequest = BloodRequest::query()->create([
            'patient_name' => $data['patient_name'],
            'blood_group' => $data['blood_group'],
            'city_id' => $data['city_id'],
            'hospital' => $data['hospital'],
            'message' => $message,
            'user_id' => $request->user()->id,
            'status' => 'open',
        ]);

        return (new BloodRequestResource($bloodRequest->load('city')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, BloodRequest $bloodRequest): BloodRequestResource
    {
        $this->authorizeView($request->user(), $bloodRequest);
        $bloodRequest->load(['city', 'user']);

        return new BloodRequestResource($bloodRequest);
    }

    public function interestedDonors(Request $request, BloodRequest $bloodRequest): JsonResponse
    {
        if ((int) $bloodRequest->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        $rows = $bloodRequest->requestResponses()
            ->where('status', 'interested')
            ->with(['donor.user'])
            ->get();

        $data = $rows->map(function ($response) {
            $donor = $response->donor;
            $user = $donor?->user;

            return [
                'response_id' => $response->id,
                'donor' => [
                    'id' => $donor?->id,
                    'blood_group' => $donor?->blood_group,
                    'is_available' => $donor?->is_available,
                    'user' => $user === null ? null : [
                        'id' => $user->id,
                        'name' => $user->name,
                    ],
                ],
            ];
        });

        return response()->json(['data' => $data]);
    }

    private function authorizeView(User $user, BloodRequest $bloodRequest): void
    {
        if ((int) $bloodRequest->user_id === (int) $user->id) {
            return;
        }

        $hasResponse = $bloodRequest->requestResponses()
            ->whereHas('donor', fn ($q) => $q->where('user_id', $user->id))
            ->exists();

        if ($hasResponse) {
            return;
        }

        $donor = $user->donor;
        if (
            $donor !== null
            && $donor->is_enabled
            && $donor->blood_group === $bloodRequest->blood_group
            && (int) $user->city_id === (int) $bloodRequest->city_id
        ) {
            return;
        }

        abort(403);
    }
}
