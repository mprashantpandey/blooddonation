<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $fillable = [
        'app_name',
        'app_tagline',
        'logo_url',
        'primary_color_hex',
        'secondary_color_hex',
        'welcome_enabled',
        'welcome_title',
        'welcome_message',
        'welcome_button_text',
        'welcome_show_once',
        'feature_chat_enabled',
        'feature_referrals_enabled',
        'feature_wallet_enabled',
        'feature_redeem_enabled',
        'auth_require_phone_verification',
        'auth_min_phone_digits',
        'firebase_options_json',
        'fcm_service_account_json',
        'firebase_web_credentials_json',
    ];

    protected function casts(): array
    {
        return [
            'welcome_enabled' => 'boolean',
            'welcome_show_once' => 'boolean',
            'feature_chat_enabled' => 'boolean',
            'feature_referrals_enabled' => 'boolean',
            'feature_wallet_enabled' => 'boolean',
            'feature_redeem_enabled' => 'boolean',
            'auth_require_phone_verification' => 'boolean',
            'auth_min_phone_digits' => 'integer',
            'firebase_options_json' => 'encrypted',
            'fcm_service_account_json' => 'encrypted',
            'firebase_web_credentials_json' => 'encrypted',
        ];
    }

    public static function current(): self
    {
        $row = static::query()->first();
        if ($row !== null) {
            return $row;
        }

        return static::query()->create([
            'app_name' => 'Blood Donation',
            'primary_color_hex' => '#B71C1C',
            'welcome_enabled' => true,
            'welcome_title' => 'Welcome',
            'welcome_message' => 'Thank you for helping save lives. You can change this message from the admin panel.',
            'welcome_button_text' => 'Got it',
            'welcome_show_once' => true,
            'feature_chat_enabled' => true,
            'feature_referrals_enabled' => true,
            'feature_wallet_enabled' => true,
            'feature_redeem_enabled' => false,
            'auth_require_phone_verification' => true,
            'auth_min_phone_digits' => 10,
        ]);
    }

    public static function forgetBootstrapCache(): void
    {
        Cache::forget('api_bootstrap_v1');
    }

    /**
     * @return array<string, mixed>
     */
    public function toBootstrapPayload(): array
    {
        $firebase = [];
        if (is_string($this->firebase_options_json) && $this->firebase_options_json !== '') {
            $decoded = json_decode($this->firebase_options_json, true);
            $firebase = is_array($decoded) ? $decoded : [];
        }

        $firebasePayload = empty($firebase) ? new \stdClass : $firebase;

        $firebaseWeb = [];
        if (is_string($this->firebase_web_credentials_json) && $this->firebase_web_credentials_json !== '') {
            $decodedWeb = json_decode($this->firebase_web_credentials_json, true);
            $firebaseWeb = is_array($decodedWeb) ? $decodedWeb : [];
        }
        $firebaseWebPayload = empty($firebaseWeb) ? new \stdClass : $firebaseWeb;

        return [
            'version' => 1,
            'updated_at' => $this->updated_at?->toIso8601String(),
            'branding' => [
                'app_name' => $this->app_name,
                'app_tagline' => $this->app_tagline,
                'logo_url' => $this->logo_url,
                'primary_color_hex' => $this->primary_color_hex,
                'secondary_color_hex' => $this->secondary_color_hex,
            ],
            'welcome' => [
                'enabled' => $this->welcome_enabled,
                'title' => $this->welcome_title,
                'message' => $this->welcome_message,
                'button_text' => $this->welcome_button_text,
                'show_once' => $this->welcome_show_once,
            ],
            'features' => [
                'chat' => $this->feature_chat_enabled,
                'referrals' => $this->feature_referrals_enabled,
                'wallet' => $this->feature_wallet_enabled,
                'redeem' => $this->feature_redeem_enabled,
            ],
            'auth' => [
                'require_phone_verification' => $this->auth_require_phone_verification,
                'min_phone_digits' => $this->auth_min_phone_digits,
            ],
            'firebase' => $firebasePayload,
            'firebase_web' => $firebaseWebPayload,
        ];
    }
}
