<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RequestResponseResource;
use App\Models\BloodRequest;
use App\Models\RequestResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BloodRequestResponseController extends Controller
{
    public function store(Request $request, BloodRequest $bloodRequest): JsonResponse|RequestResponseResource
    {
        if ((int) $bloodRequest->user_id === (int) $request->user()->id) {
            return response()->json(['message' => 'You cannot respond to your own request.'], 422);
        }

        if ($bloodRequest->status !== 'open') {
            return response()->json(['message' => 'This request is no longer open.'], 422);
        }

        $data = $request->validate([
            'status' => 'required|in:interested,ignored',
        ]);

        $donor = $request->user()->donor;
        if ($donor === null || ! $donor->is_enabled) {
            return response()->json([
                'message' => 'You need an active donor profile to respond.',
            ], 422);
        }

        if ($donor->blood_group !== $bloodRequest->blood_group) {
            return response()->json(['message' => 'Your blood group does not match this request.'], 422);
        }

        if ((int) $request->user()->city_id !== (int) $bloodRequest->city_id) {
            return response()->json(['message' => 'You must be in the same city as this request.'], 422);
        }

        $response = RequestResponse::query()->updateOrCreate(
            [
                'request_id' => $bloodRequest->id,
                'donor_id' => $donor->id,
            ],
            ['status' => $data['status']]
        );
        $response->load('donor');

        return (new RequestResponseResource($response))
            ->response()
            ->setStatusCode($response->wasRecentlyCreated ? 201 : 200);
    }
}
