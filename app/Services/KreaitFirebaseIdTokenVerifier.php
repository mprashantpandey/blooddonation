<?php

namespace App\Services;

use App\Contracts\FirebaseIdTokenVerifier;
use App\Models\AppSetting;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
use Kreait\Firebase\Factory;
use RuntimeException;

class KreaitFirebaseIdTokenVerifier implements FirebaseIdTokenVerifier
{
    public function verify(string $idToken): array
    {
        $auth = $this->auth();

        try {
            $verified = $auth->verifyIdToken($idToken);
        } catch (FailedToVerifyToken $e) {
            throw new RuntimeException('Invalid Firebase ID token.', 0, $e);
        }

        $claims = $verified->claims();
        $uid = (string) $claims->get('sub');
        $phone = $claims->has('phone_number') ? (string) $claims->get('phone_number') : null;

        if ($uid === '') {
            throw new RuntimeException('Firebase token missing uid.');
        }

        return [
            'uid' => $uid,
            'phone_number' => $phone ?: null,
        ];
    }

    private function auth(): Auth
    {
        $factory = new Factory();
        $sa = $this->serviceAccount();
        if ($sa !== null) {
            $factory = $factory->withServiceAccount($sa);
        }

        $projectId = (string) (config('firebase.project_id') ?? env('FCM_PROJECT_ID', ''));
        if ($projectId !== '') {
            $factory = $factory->withProjectId($projectId);
        }

        return $factory->createAuth();
    }

    /**
     * @return array<string, mixed>|string|null
     */
    private function serviceAccount(): array|string|null
    {
        $path = env('FIREBASE_SERVICE_ACCOUNT_PATH');
        if (is_string($path) && $path !== '' && file_exists($path)) {
            return $path;
        }

        $rawJson = env('FIREBASE_SERVICE_ACCOUNT_JSON');
        if (is_string($rawJson) && trim($rawJson) !== '') {
            $decoded = json_decode($rawJson, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $b64 = env('FIREBASE_SERVICE_ACCOUNT_BASE64');
        if (is_string($b64) && trim($b64) !== '') {
            $decodedJson = base64_decode($b64, true);
            if (is_string($decodedJson) && $decodedJson !== '') {
                $decoded = json_decode($decodedJson, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        // Fallback: use encrypted service account JSON from app settings (same as FCM).
        try {
            $settings = AppSetting::current();
            $s = $settings->fcm_service_account_json;
            if (is_string($s) && trim($s) !== '') {
                $decoded = json_decode($s, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        } catch (\Throwable) {
            // ignore (e.g., during early migrations/tests)
        }

        return null;
    }
}

