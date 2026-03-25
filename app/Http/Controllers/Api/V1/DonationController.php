<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BloodRequest;
use App\Models\Donation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonationController extends Controller
{
    public function mine(Request $request): JsonResponse
    {
        $user = $request->user();
        $donor = $user->donor;
        if ($donor === null) {
            return response()->json(['data' => []]);
        }

        $rows = Donation::query()
            ->with(['bloodRequest.city'])
            ->where('donor_id', $donor->id)
            ->latest()
            ->paginate(20);

        $data = collect($rows->items())->map(fn (Donation $d) => $this->serializeDonation($d))->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'total' => $rows->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $donor = $user->donor;
        if ($donor === null || ! $donor->is_enabled) {
            return response()->json(['message' => 'You need an active donor profile to submit a donation.'], 422);
        }

        $data = $request->validate([
            'request_id' => ['required', 'integer', 'exists:blood_requests,id'],
            'hospital_name' => ['nullable', 'string', 'max:255'],
        ]);

        $bloodRequest = BloodRequest::query()->findOrFail($data['request_id']);

        // Only allow donations for same city & blood group (basic guard).
        if ((int) $bloodRequest->city_id !== (int) $user->city_id || $bloodRequest->blood_group !== $donor->blood_group) {
            return response()->json(['message' => 'This request is not eligible for your donor profile.'], 422);
        }

        $donation = Donation::query()->firstOrCreate(
            ['donor_id' => $donor->id, 'request_id' => $bloodRequest->id],
            [
                'hospital_name' => $data['hospital_name'] ?? null,
                'status' => 'pending',
                'points' => 0,
            ]
        );

        if (! $donation->wasRecentlyCreated && array_key_exists('hospital_name', $data)) {
            $donation->hospital_name = $data['hospital_name'];
            $donation->save();
        }

        $donation->load(['bloodRequest.city']);

        return response()->json(['data' => $this->serializeDonation($donation)], $donation->wasRecentlyCreated ? 201 : 200);
    }

    public function uploadProof(Request $request, Donation $donation): JsonResponse
    {
        $user = $request->user();
        $donor = $user->donor;
        if ($donor === null || (int) $donation->donor_id !== (int) $donor->id) {
            abort(403);
        }

        $request->validate([
            'proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
        ]);

        // Replace old file if present.
        if (is_string($donation->proof_image) && $donation->proof_image !== '') {
            $old = $this->proofPathToStoragePath($donation->proof_image);
            if ($old !== null) {
                Storage::disk('public')->delete($old);
            }
        }

        $path = $request->file('proof')->store('donation-proofs', 'public');
        $donation->proof_image = Storage::disk('public')->url($path);
        $donation->status = 'pending';
        $donation->save();

        $donation->load(['bloodRequest.city']);

        return response()->json(['data' => $this->serializeDonation($donation)]);
    }

    private function serializeDonation(Donation $d): array
    {
        return [
            'id' => $d->id,
            'request_id' => $d->request_id,
            'status' => $d->status,
            'points' => (int) $d->points,
            'hospital_name' => $d->hospital_name,
            'proof_image' => $d->proof_image,
            'created_at' => $d->created_at?->toIso8601String(),
            'request' => $d->relationLoaded('bloodRequest') && $d->bloodRequest
                ? [
                    'id' => $d->bloodRequest->id,
                    'blood_group' => $d->bloodRequest->blood_group,
                    'patient_name' => $d->bloodRequest->patient_name,
                    'city' => $d->bloodRequest->relationLoaded('city') && $d->bloodRequest->city
                        ? ['id' => $d->bloodRequest->city->id, 'city_name' => $d->bloodRequest->city->city_name]
                        : null,
                ]
                : null,
        ];
    }

    private function proofPathToStoragePath(string $urlOrPath): ?string
    {
        // We store proof_image as a URL right now, but try to map it back.
        // If it already looks like a storage path, return it.
        if (str_starts_with($urlOrPath, 'donation-proofs/')) {
            return $urlOrPath;
        }
        $needle = '/storage/';
        $pos = strpos($urlOrPath, $needle);
        if ($pos === false) {
            return null;
        }
        return substr($urlOrPath, $pos + strlen($needle));
    }
}

