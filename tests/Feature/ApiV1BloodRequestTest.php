<?php

namespace Tests\Feature;

use App\Models\BloodRequest;
use App\Models\City;
use App\Models\Donor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiV1BloodRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_blood_request(): void
    {
        Bus::fake();

        $city = City::factory()->create(['status' => 'active']);
        $user = User::factory()->create(['city_id' => $city->id]);
        $token = $user->createToken('t')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/v1/blood-requests', [
            'patient_name' => 'Someone',
            'blood_group' => 'O+',
            'city_id' => $city->id,
            'hospital' => 'City Hospital',
            'message' => 'Urgent',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.blood_group', 'O+');

        $this->assertSame(1, BloodRequest::query()->count());
    }

    public function test_donor_can_respond_interested(): void
    {
        $city = City::factory()->create(['status' => 'active']);

        $requester = User::factory()->create(['city_id' => $city->id]);
        $donorUser = User::factory()->create(['city_id' => $city->id]);
        Donor::query()->create([
            'user_id' => $donorUser->id,
            'blood_group' => 'A+',
            'is_available' => true,
            'is_enabled' => true,
            'is_verified' => false,
        ]);

        $bloodRequest = BloodRequest::query()->create([
            'patient_name' => 'Patient',
            'user_id' => $requester->id,
            'blood_group' => 'A+',
            'city_id' => $city->id,
            'hospital' => 'General',
            'message' => null,
            'status' => 'open',
        ]);

        $token = $donorUser->createToken('t')->plainTextToken;

        $response = $this->withToken($token)->postJson("/api/v1/blood-requests/{$bloodRequest->id}/respond", [
            'status' => 'interested',
        ]);

        $response->assertSuccessful()->assertJsonPath('data.status', 'interested');

        Sanctum::actingAs($requester, ['*']);
        $this->getJson("/api/v1/blood-requests/{$bloodRequest->id}/interested-donors")
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }
}
