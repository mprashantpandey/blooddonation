<?php

namespace Tests\Feature;

use App\Jobs\NotifyMatchingDonorsOfBloodRequestJob;
use App\Models\BloodRequest;
use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class FcmDispatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_blood_request_dispatches_fcm_job(): void
    {
        Bus::fake();

        $city = City::query()->create([
            'city_name' => 'Test City',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'city_id' => $city->id,
        ]);

        BloodRequest::query()->create([
            'patient_name' => 'Patient',
            'user_id' => $user->id,
            'blood_group' => 'O+',
            'city_id' => $city->id,
            'hospital' => 'General',
            'message' => 'Urgent',
            'status' => 'open',
        ]);

        Bus::assertDispatched(NotifyMatchingDonorsOfBloodRequestJob::class);
    }
}
