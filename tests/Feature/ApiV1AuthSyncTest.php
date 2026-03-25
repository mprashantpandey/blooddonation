<?php

namespace Tests\Feature;

use App\Contracts\FirebaseIdTokenVerifier;
use App\Models\City;
use App\Models\Referral;
use App\Models\User;
use App\Models\WalletEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1AuthSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_creates_user_and_token(): void
    {
        $this->app->instance(FirebaseIdTokenVerifier::class, new class implements FirebaseIdTokenVerifier {
            public function verify(string $idToken): array
            {
                return ['uid' => 'uid-abc', 'phone_number' => '+919876543210'];
            }
        });

        $city = City::factory()->create(['status' => 'active']);

        $response = $this->postJson('/api/v1/auth/sync', [
            'id_token' => str_repeat('a', 60),
            'name' => 'Test Patient',
            'city_id' => $city->id,
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'token_type', 'is_new', 'profile_complete', 'user' => ['id', 'mobile', 'referral_code']]);

        $this->assertDatabaseHas('users', [
            'firebase_uid' => 'uid-abc',
            'mobile' => '+919876543210',
        ]);
    }

    public function test_sync_with_referral_awards_wallet_points(): void
    {
        $this->app->instance(FirebaseIdTokenVerifier::class, new class implements FirebaseIdTokenVerifier {
            public function verify(string $idToken): array
            {
                return ['uid' => 'uid-new', 'phone_number' => '+919000000001'];
            }
        });

        $city = City::factory()->create(['status' => 'active']);
        $referrer = User::factory()->create([
            'referral_code' => 'INVITE01',
            'city_id' => $city->id,
        ]);

        $response = $this->postJson('/api/v1/auth/sync', [
            'id_token' => str_repeat('a', 60),
            'name' => 'Referred User',
            'city_id' => $city->id,
            'referral_code' => 'invite01',
        ]);

        $response->assertOk();
        $newUser = User::query()->where('firebase_uid', 'uid-new')->first();
        $this->assertNotNull($newUser);
        $this->assertSame($referrer->id, $newUser->referred_by_user_id);

        $this->assertTrue(
            Referral::query()->where('referrer_id', $referrer->id)->where('new_user_id', $newUser->id)->exists()
        );
        $this->assertSame(50, WalletEntry::query()->where('user_id', $referrer->id)->sum('points'));
        $this->assertSame(20, WalletEntry::query()->where('user_id', $newUser->id)->sum('points'));
    }

    public function test_me_requires_token(): void
    {
        $this->getJson('/api/v1/me')->assertUnauthorized();
    }
}
