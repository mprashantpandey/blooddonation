<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BootstrapApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_bootstrap_returns_json(): void
    {
        AppSetting::current();

        $response = $this->getJson('/api/v1/bootstrap');

        $response->assertOk()
            ->assertJsonPath('version', 1)
            ->assertJsonStructure([
                'version',
                'updated_at',
                'branding' => ['app_name', 'primary_color_hex'],
                'welcome',
                'features',
                'auth',
                'firebase',
                'firebase_web',
            ]);
    }
}
