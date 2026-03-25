<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    private function settings(): AppSetting
    {
        return AppSetting::current();
    }

    private function saveAndRedirect(AppSetting $model, string $route, string $message): RedirectResponse
    {
        $model->save();
        AppSetting::forgetBootstrapCache();

        return redirect()->route($route)->with('status', $message);
    }

    public function editBranding(): View
    {
        return view('admin.settings.branding', [
            'settings' => $this->settings(),
        ]);
    }

    public function updateBranding(Request $request): RedirectResponse
    {
        $request->merge([
            'secondary_color_hex' => $request->filled('secondary_color_hex')
                ? $request->string('secondary_color_hex')->trim()->value()
                : null,
        ]);

        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:255'],
            'app_tagline' => ['nullable', 'string', 'max:255'],
            'logo_url' => ['nullable', 'string', 'max:2048'],
            'primary_color_hex' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color_hex' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        if (empty($validated['secondary_color_hex'])) {
            $validated['secondary_color_hex'] = null;
        }

        $settings = $this->settings();
        $settings->fill($validated);

        return $this->saveAndRedirect($settings, 'admin.settings.branding', 'Branding saved.');
    }

    public function editWelcome(): View
    {
        return view('admin.settings.welcome', [
            'settings' => $this->settings(),
        ]);
    }

    public function updateWelcome(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'welcome_title' => ['nullable', 'string', 'max:255'],
            'welcome_message' => ['nullable', 'string', 'max:5000'],
            'welcome_button_text' => ['nullable', 'string', 'max:64'],
        ]);

        $validated['welcome_enabled'] = $request->boolean('welcome_enabled');
        $validated['welcome_show_once'] = $request->boolean('welcome_show_once');

        $settings = $this->settings();
        $settings->fill($validated);

        return $this->saveAndRedirect($settings, 'admin.settings.welcome', 'Welcome dialog saved.');
    }

    public function editFeatures(): View
    {
        return view('admin.settings.features', [
            'settings' => $this->settings(),
        ]);
    }

    public function updateFeatures(Request $request): RedirectResponse
    {
        $settings = $this->settings();
        $settings->fill([
            'feature_chat_enabled' => $request->boolean('feature_chat_enabled'),
            'feature_referrals_enabled' => $request->boolean('feature_referrals_enabled'),
            'feature_wallet_enabled' => $request->boolean('feature_wallet_enabled'),
            'feature_redeem_enabled' => $request->boolean('feature_redeem_enabled'),
        ]);

        return $this->saveAndRedirect($settings, 'admin.settings.features', 'Modules saved.');
    }

    public function editRewards(): View
    {
        return view('admin.settings.rewards', [
            'settings' => $this->settings(),
        ]);
    }

    public function updateRewards(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'points_donation_default' => ['required', 'integer', 'min:0', 'max:10000'],
            'points_referral_referrer' => ['required', 'integer', 'min:0', 'max:10000'],
            'points_referral_new_user' => ['required', 'integer', 'min:0', 'max:10000'],
            'verified_after_approved_donations' => ['required', 'integer', 'min:1', 'max:50'],

            'badge_donation_1_threshold' => ['required', 'integer', 'min:1', 'max:50'],
            'badge_donation_3_threshold' => ['required', 'integer', 'min:1', 'max:50'],
            'badge_donation_5_threshold' => ['required', 'integer', 'min:1', 'max:50'],
            'badge_referral_threshold' => ['required', 'integer', 'min:1', 'max:200'],

            'badge_donation_1_name' => ['required', 'string', 'max:64'],
            'badge_donation_3_name' => ['required', 'string', 'max:64'],
            'badge_donation_5_name' => ['required', 'string', 'max:64'],
            'badge_referral_name' => ['required', 'string', 'max:64'],
            'badge_verified_name' => ['required', 'string', 'max:64'],
        ]);

        $settings = $this->settings();
        $settings->fill($validated);

        return $this->saveAndRedirect($settings, 'admin.settings.rewards', 'Rewards settings saved.');
    }

    public function editAuth(): View
    {
        return view('admin.settings.auth', [
            'settings' => $this->settings(),
        ]);
    }

    public function updateAuth(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'auth_min_phone_digits' => ['required', 'integer', 'min:6', 'max:15'],
        ]);

        $validated['auth_require_phone_verification'] = $request->boolean('auth_require_phone_verification');

        $settings = $this->settings();
        $settings->fill($validated);

        return $this->saveAndRedirect($settings, 'admin.settings.auth', 'Sign-in settings saved.');
    }

    public function editFirebase(): View
    {
        $settings = $this->settings();
        $firebasePreview = '';
        if (is_string($settings->firebase_options_json) && $settings->firebase_options_json !== '') {
            $firebasePreview = $settings->firebase_options_json;
        }

        $fcmServicePreview = '';
        if (is_string($settings->fcm_service_account_json) && $settings->fcm_service_account_json !== '') {
            $fcmServicePreview = $settings->fcm_service_account_json;
        }

        $webPreview = '';
        if (is_string($settings->firebase_web_credentials_json) && $settings->firebase_web_credentials_json !== '') {
            $webPreview = $settings->firebase_web_credentials_json;
        }

        return view('admin.settings.firebase', [
            'settings' => $settings,
            'firebase_preview' => $firebasePreview,
            'fcm_service_preview' => $fcmServicePreview,
            'firebase_web_preview' => $webPreview,
        ]);
    }

    public function updateFirebase(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'firebase_options_json' => ['nullable', 'string', 'max:20000'],
        ]);

        $firebaseRaw = $request->input('firebase_options_json');
        if ($firebaseRaw === null || trim((string) $firebaseRaw) === '') {
            $validated['firebase_options_json'] = null;
        } else {
            json_decode((string) $firebaseRaw);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['firebase_options_json' => 'Must be valid JSON.'])->withInput();
            }
            $validated['firebase_options_json'] = $firebaseRaw;
        }

        $settings = $this->settings();
        $settings->fill($validated);

        return $this->saveAndRedirect($settings, 'admin.settings.firebase', 'Mobile Firebase JSON saved.');
    }

    public function updateFcmServiceAccount(Request $request): RedirectResponse
    {
        $raw = $request->input('fcm_service_account_json');

        if ($raw === null || trim((string) $raw) === '') {
            $settings = $this->settings();
            $settings->fill(['fcm_service_account_json' => null]);

            return $this->saveAndRedirect($settings, 'admin.settings.firebase', 'Server service account removed. FCM will use .env if set.');
        }

        $request->validate([
            'fcm_service_account_json' => ['required', 'string', 'max:65535'],
        ]);

        $decoded = json_decode((string) $raw, true);
        if (! is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['fcm_service_account_json' => 'Must be valid JSON.'])->withInput();
        }

        foreach (['private_key', 'client_email'] as $key) {
            if (empty($decoded[$key])) {
                return back()->withErrors([
                    'fcm_service_account_json' => "Service account JSON must include \"{$key}\".",
                ])->withInput();
            }
        }

        $settings = $this->settings();
        $settings->fill(['fcm_service_account_json' => $raw]);

        return $this->saveAndRedirect($settings, 'admin.settings.firebase', 'FCM server service account saved (encrypted). Env path / JSON still overrides if set.');
    }

    public function updateFirebaseWeb(Request $request): RedirectResponse
    {
        $raw = $request->input('firebase_web_credentials_json');

        if ($raw === null || trim((string) $raw) === '') {
            $settings = $this->settings();
            $settings->fill(['firebase_web_credentials_json' => null]);

            return $this->saveAndRedirect($settings, 'admin.settings.firebase', 'Web credentials cleared from bootstrap API.');
        }

        $request->validate([
            'firebase_web_credentials_json' => ['required', 'string', 'max:20000'],
        ]);

        json_decode((string) $raw);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->withErrors(['firebase_web_credentials_json' => 'Must be valid JSON.'])->withInput();
        }

        $settings = $this->settings();
        $settings->fill(['firebase_web_credentials_json' => $raw]);

        return $this->saveAndRedirect($settings, 'admin.settings.firebase', 'Web credentials saved. Exposed as firebase_web in GET /api/v1/bootstrap.');
    }
}
