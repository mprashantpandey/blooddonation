<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Contracts\FirebaseIdTokenVerifier;
use App\Models\City;
use App\Models\Referral;
use App\Models\User;
use App\Models\WalletEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function sync(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_token' => 'required|string|min:50|max:10000',
            'name' => 'required|string|max:255',
            'city_id' => 'nullable|integer|exists:cities,id',
            'area' => 'nullable|string|max:255',
            'referral_code' => 'nullable|string|max:32',
            'fcm_token' => 'nullable|string|max:4096',
        ]);

        /** @var FirebaseIdTokenVerifier $verifier */
        $verifier = app(FirebaseIdTokenVerifier::class);
        $claims = $verifier->verify($data['id_token']);
        $firebaseUid = $claims['uid'] ?? '';
        $phone = $claims['phone_number'] ?? null;

        if (! is_string($firebaseUid) || trim($firebaseUid) === '') {
            return response()->json(['message' => 'Invalid Firebase token (missing uid).'], 422);
        }
        if (! is_string($phone) || trim($phone) === '') {
            return response()->json(['message' => 'Phone sign-in required.'], 422);
        }

        $mobile = trim($phone);

        if (! empty($data['city_id'])) {
            $city = City::query()->where('id', $data['city_id'])->where('status', 'active')->first();
            if ($city === null) {
                return response()->json(['message' => 'Selected city is not available.'], 422);
            }
        }

        $user = User::query()->where('firebase_uid', $firebaseUid)->first();

        if ($user === null) {
            if (User::query()->where('mobile', $mobile)->exists()) {
                return response()->json(['message' => 'This mobile number is already registered.'], 422);
            }

            $user = DB::transaction(function () use ($data, $firebaseUid, $mobile) {
                $referrerId = null;
                if (! empty($data['referral_code'])) {
                    $ref = strtoupper(trim($data['referral_code']));
                    $referrer = User::query()->where('referral_code', $ref)->first();
                    if ($referrer !== null) {
                        $referrerId = $referrer->id;
                    }
                }

                $newUser = User::query()->create([
                    'name' => $data['name'],
                    'mobile' => $mobile,
                    'firebase_uid' => $firebaseUid,
                    'city_id' => $data['city_id'] ?? null,
                    'area' => $data['area'] ?? null,
                    'referred_by_user_id' => $referrerId,
                    'referral_code' => $this->uniqueReferralCode(),
                    'fcm_token' => $data['fcm_token'] ?? null,
                    'password' => null,
                ]);

                if ($referrerId !== null) {
                    $referral = Referral::query()->firstOrCreate(
                        ['new_user_id' => $newUser->id],
                        ['referrer_id' => $referrerId]
                    );
                    if ($referral->wasRecentlyCreated) {
                        WalletEntry::query()->create([
                            'user_id' => $referrerId,
                            'points' => 50,
                            'type' => 'referral',
                            'description' => 'Referral bonus',
                        ]);
                        WalletEntry::query()->create([
                            'user_id' => $newUser->id,
                            'points' => 20,
                            'type' => 'referral',
                            'description' => 'Welcome referral bonus',
                        ]);
                    }
                }

                return $newUser;
            });
        } else {
            if (User::query()->where('mobile', $mobile)->where('id', '!=', $user->id)->exists()) {
                return response()->json(['message' => 'This mobile number is already in use.'], 422);
            }

            $user->name = $data['name'];
            $user->mobile = $mobile;

            if (array_key_exists('city_id', $data)) {
                $user->city_id = $data['city_id'];
            }
            if (array_key_exists('area', $data)) {
                $user->area = $data['area'];
            }
            if (array_key_exists('fcm_token', $data)) {
                $user->fcm_token = $data['fcm_token'];
            }

            $user->save();
        }

        $token = $user->createToken('mobile')->plainTextToken;
        $user->load(['donor', 'city']);

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => (new UserResource($user))->toArray($request),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user()->load(['donor', 'city']);

        return response()->json(['user' => (new UserResource($user))->toArray($request)]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'city_id' => 'nullable|integer|exists:cities,id',
            'area' => 'nullable|string|max:255',
        ]);

        if (array_key_exists('city_id', $data) && $data['city_id'] !== null) {
            $city = City::query()->where('id', $data['city_id'])->where('status', 'active')->first();
            if ($city === null) {
                return response()->json(['message' => 'Selected city is not available.'], 422);
            }
        }

        /** @var User $user */
        $user = $request->user();
        $user->fill($data);
        $user->save();
        $user->load(['donor', 'city']);

        return response()->json(['user' => (new UserResource($user))->toArray($request)]);
    }

    public function updateFcmToken(Request $request): JsonResponse
    {
        $data = $request->validate([
            'fcm_token' => 'required|string|max:4096',
        ]);

        /** @var User $user */
        $user = $request->user();
        $user->fcm_token = $data['fcm_token'];
        $user->save();

        return response()->json(['ok' => true]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['ok' => true]);
    }

    private function uniqueReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::query()->where('referral_code', $code)->exists());

        return $code;
    }
}
