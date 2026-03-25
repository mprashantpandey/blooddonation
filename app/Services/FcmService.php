<?php

namespace App\Services;

use App\Models\AppSetting;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class FcmService
{
    private const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

    /** @var array<string, mixed>|null */
    private ?array $serviceAccount = null;

    public function __construct()
    {
        $this->serviceAccount = $this->loadServiceAccount();
    }

    public function isConfigured(): bool
    {
        return is_array($this->serviceAccount)
            && isset($this->serviceAccount['private_key'], $this->serviceAccount['client_email']);
    }

    public function projectId(): string
    {
        $fromEnv = config('firebase.project_id');

        return is_string($fromEnv) && $fromEnv !== ''
            ? $fromEnv
            : (string) ($this->serviceAccount['project_id'] ?? '');
    }

    /**
     * @param  array<string, string|int|float|bool>  $data  FCM data payload (values become strings)
     */
    public function sendToToken(
        string $deviceToken,
        string $title,
        string $body,
        array $data = []
    ): bool {
        if (! $this->isConfigured()) {
            Log::warning('FCM: service account not configured, skipping send.');

            return false;
        }

        $projectId = $this->projectId();
        if ($projectId === '') {
            Log::error('FCM: project id missing (set FCM_PROJECT_ID or project_id in service account JSON).');

            return false;
        }

        $url = sprintf(
            'https://fcm.googleapis.com/v1/projects/%s/messages:send',
            $projectId
        );

        $message = [
            'token' => $deviceToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'android' => [
                'priority' => 'HIGH',
            ],
            'apns' => [
                'payload' => [
                    'aps' => [
                        'sound' => 'default',
                    ],
                ],
            ],
        ];

        $stringData = $this->stringifyData($data);
        if ($stringData !== []) {
            $message['data'] = $stringData;
        }

        try {
            $token = $this->accessToken();
            $response = Http::timeout(15)
                ->withToken($token)
                ->acceptJson()
                ->post($url, ['message' => $message]);

            if ($response->successful()) {
                return true;
            }

            Log::warning('FCM send failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (Throwable $e) {
            Log::error('FCM send exception: '.$e->getMessage(), ['exception' => $e]);

            return false;
        }
    }

    /**
     * @param  list<string>  $deviceTokens
     * @param  array<string, string|int|float|bool>  $data
     * @return array{sent: int, failed: int}
     */
    public function sendToTokens(
        array $deviceTokens,
        string $title,
        string $body,
        array $data = []
    ): array {
        $sent = 0;
        $failed = 0;

        foreach ($deviceTokens as $t) {
            $t = trim($t);
            if ($t === '') {
                continue;
            }
            if ($this->sendToToken($t, $title, $body, $data)) {
                $sent++;
            } else {
                $failed++;
            }
        }

        return ['sent' => $sent, 'failed' => $failed];
    }

    private function accessToken(): string
    {
        $creds = new ServiceAccountCredentials(self::SCOPE, $this->serviceAccount);
        $token = $creds->fetchAuthToken();

        if (! is_array($token) || empty($token['access_token'])) {
            throw new InvalidArgumentException('Could not obtain OAuth access token for FCM.');
        }

        return (string) $token['access_token'];
    }

    /**
     * @param  array<string, string|int|float|bool>  $data
     * @return array<string, string>
     */
    private function stringifyData(array $data): array
    {
        $out = [];
        foreach ($data as $k => $v) {
            $out[(string) $k] = is_string($v) ? $v : (string) $v;
        }

        return $out;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function loadServiceAccount(): ?array
    {
        $path = config('firebase.service_account_path');
        if (is_string($path) && $path !== '' && is_readable($path)) {
            $raw = file_get_contents($path);
            if ($raw === false) {
                return null;
            }
            $decoded = json_decode($raw, true);

            return is_array($decoded) ? $decoded : null;
        }

        $json = config('firebase.service_account_json');
        if (is_string($json) && $json !== '') {
            $decoded = json_decode($json, true);

            return is_array($decoded) ? $decoded : null;
        }

        $b64 = config('firebase.service_account_base64');
        if (is_string($b64) && $b64 !== '') {
            $raw = base64_decode($b64, true);
            if ($raw === false) {
                return null;
            }
            $decoded = json_decode($raw, true);

            return is_array($decoded) ? $decoded : null;
        }

        return $this->serviceAccountFromAppSettings();
    }

    /**
     * @return array<string, mixed>|null
     */
    private function serviceAccountFromAppSettings(): ?array
    {
        try {
            $row = AppSetting::query()->first();
        } catch (Throwable) {
            return null;
        }

        if ($row === null) {
            return null;
        }

        $raw = $row->fcm_service_account_json;
        if (! is_string($raw) || $raw === '') {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return null;
        }

        if (! isset($decoded['private_key'], $decoded['client_email'])) {
            return null;
        }

        return $decoded;
    }
}
