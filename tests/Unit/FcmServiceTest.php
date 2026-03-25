<?php

namespace Tests\Unit;

use App\Services\FcmService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FcmServiceTest extends TestCase
{
    #[Test]
    public function fcm_service_is_not_configured_without_credentials(): void
    {
        config([
            'firebase.service_account_path' => null,
            'firebase.service_account_json' => null,
            'firebase.service_account_base64' => null,
            'firebase.project_id' => null,
        ]);

        $fcm = new FcmService;

        $this->assertFalse($fcm->isConfigured());
    }
}
