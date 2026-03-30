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
        $this->assertSame('[EMERGENCY] Urgent', (string) BloodRequest::query()->first()?->message);
    }

    public function test_new_request_replaces_previous_open_request_for_same_user(): void
    {
        $city = City::factory()->create(['status' => 'active']);
        $user = User::factory()->create(['city_id' => $city->id]);
        $token = $user->createToken('t')->plainTextToken;

        BloodRequest::query()->create([
            'patient_name' => 'Old Patient',
            'user_id' => $user->id,
            'blood_group' => 'A+',
            'city_id' => $city->id,
            'hospital' => 'Old Hospital',
            'message' => '[EMERGENCY] Previous',
            'status' => 'open',
        ]);

        $response = $this->withToken($token)->postJson('/api/v1/blood-requests', [
            'patient_name' => 'New Patient',
            'blood_group' => 'O+',
            'city_id' => $city->id,
            'hospital' => 'New Hospital',
            'message' => 'Need blood now',
        ]);

        $response->assertCreated();

        $openCount = BloodRequest::query()
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->count();
        $closedCount = BloodRequest::query()
            ->where('user_id', $user->id)
            ->where('status', 'closed')
            ->count();

        $this->assertSame(1, $openCount);
        $this->assertSame(1, $closedCount);
        $this->assertSame('[EMERGENCY] Need blood now', (string) BloodRequest::query()->latest('id')->first()?->message);
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

    public function test_donor_feed_is_citywise_and_open_requests_only(): void
    {
        $cityA = City::factory()->create(['status' => 'active']);
        $cityB = City::factory()->create(['status' => 'active']);

        $donorUser = User::factory()->create(['city_id' => $cityA->id]);
        Donor::query()->create([
            'user_id' => $donorUser->id,
            'blood_group' => 'A+',
            'is_available' => true,
            'is_enabled' => true,
            'is_verified' => false,
        ]);

        $sameCityRequester = User::factory()->create(['city_id' => $cityA->id]);
        $otherCityRequester = User::factory()->create(['city_id' => $cityB->id]);

        $visible = BloodRequest::query()->create([
            'patient_name' => 'Visible',
            'user_id' => $sameCityRequester->id,
            'blood_group' => 'A+',
            'city_id' => $cityA->id,
            'hospital' => 'A Hospital',
            'message' => '[EMERGENCY] Same city',
            'status' => 'open',
        ]);

        $visibleOtherGroup = BloodRequest::query()->create([
            'patient_name' => 'Visible 2',
            'user_id' => $sameCityRequester->id,
            'blood_group' => 'O+',
            'city_id' => $cityA->id,
            'hospital' => 'A Hospital',
            'message' => '[EMERGENCY] Same city other group',
            'status' => 'open',
        ]);

        BloodRequest::query()->create([
            'patient_name' => 'Other City',
            'user_id' => $otherCityRequester->id,
            'blood_group' => 'A+',
            'city_id' => $cityB->id,
            'hospital' => 'B Hospital',
            'message' => '[EMERGENCY] Other city',
            'status' => 'open',
        ]);

        $visiblePlanned = BloodRequest::query()->create([
            'patient_name' => 'Non Emergency',
            'user_id' => $sameCityRequester->id,
            'blood_group' => 'A+',
            'city_id' => $cityA->id,
            'hospital' => 'A Hospital 2',
            'message' => '[PLANNED] Future',
            'status' => 'open',
        ]);

        $token = $donorUser->createToken('t')->plainTextToken;
        $response = $this->withToken($token)->getJson('/api/v1/donor/feed');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonFragment(['id' => $visible->id])
            ->assertJsonFragment(['id' => $visiblePlanned->id])
            ->assertJsonFragment(['id' => $visibleOtherGroup->id]);
    }
}
