<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WalletEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1WalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_wallet_summary_requires_auth(): void
    {
        $this->getJson('/api/v1/wallet')->assertUnauthorized();
    }

    public function test_wallet_summary_returns_balance(): void
    {
        $user = User::factory()->create();
        WalletEntry::query()->create(['user_id' => $user->id, 'points' => 10, 'type' => 'referral', 'description' => null]);
        WalletEntry::query()->create(['user_id' => $user->id, 'points' => 5, 'type' => 'donation', 'description' => null]);

        $token = $user->createToken('t')->plainTextToken;
        $this->withToken($token)->getJson('/api/v1/wallet')
            ->assertOk()
            ->assertJsonPath('data.points_balance', 15);
    }
}

