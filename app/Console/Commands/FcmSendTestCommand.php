<?php

namespace App\Console\Commands;

use App\Services\FcmService;
use Illuminate\Console\Command;

class FcmSendTestCommand extends Command
{
    protected $signature = 'fcm:test
                            {token : FCM device registration token}
                            {--title=FCM test : Notification title}
                            {--body=Hello from Laravel : Notification body}';

    protected $description = 'Send a test FCM HTTP v1 notification (requires service account env).';

    public function handle(FcmService $fcm): int
    {
        if (! $fcm->isConfigured()) {
            $this->error('FCM is not configured. Set FIREBASE_SERVICE_ACCOUNT_PATH, FIREBASE_SERVICE_ACCOUNT_JSON, or FIREBASE_SERVICE_ACCOUNT_BASE64.');

            return self::FAILURE;
        }

        $this->info('Project: '.$fcm->projectId());

        $ok = $fcm->sendToToken(
            $this->argument('token'),
            (string) $this->option('title'),
            (string) $this->option('body'),
            ['type' => 'test', 'source' => 'artisan'],
        );

        if ($ok) {
            $this->info('Notification sent.');

            return self::SUCCESS;
        }

        $this->error('Send failed (check logs).');

        return self::FAILURE;
    }
}
