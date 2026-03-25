<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Badge;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Referral;

class RewardsService
{
    public function applyAfterDonationApproved(Donation $donation): void
    {
        $donor = $donation->donor;
        if (! ($donor instanceof Donor)) {
            $donation->loadMissing('donor');
            $donor = $donation->donor;
        }
        if (! ($donor instanceof Donor)) {
            return;
        }

        $settings = AppSetting::current();
        $donorUserId = $donor->user_id;

        $approvedCount = Donation::query()
            ->where('donor_id', $donor->id)
            ->where('status', 'approved')
            ->count();

        $verifiedAfter = max(1, (int) $settings->verified_after_approved_donations);
        if ($approvedCount >= $verifiedAfter && ! $donor->is_verified) {
            $donor->is_verified = true;
            $donor->save();

            $this->ensureBadge($donorUserId, (string) $settings->badge_verified_name);
        }

        $this->applyDonationBadges($donorUserId, $approvedCount, $settings);
    }

    public function applyAfterReferralCreated(int $referrerUserId): void
    {
        $settings = AppSetting::current();
        if (! $settings->feature_referrals_enabled) {
            return;
        }

        $count = Referral::query()->where('referrer_id', $referrerUserId)->count();
        $threshold = max(1, (int) $settings->badge_referral_threshold);
        if ($count >= $threshold) {
            $this->ensureBadge($referrerUserId, (string) $settings->badge_referral_name);
        }
    }

    private function applyDonationBadges(int $userId, int $approvedDonationCount, AppSetting $settings): void
    {
        $pairs = [
            [(int) $settings->badge_donation_1_threshold, (string) $settings->badge_donation_1_name],
            [(int) $settings->badge_donation_3_threshold, (string) $settings->badge_donation_3_name],
            [(int) $settings->badge_donation_5_threshold, (string) $settings->badge_donation_5_name],
        ];

        foreach ($pairs as [$threshold, $name]) {
            $t = max(1, (int) $threshold);
            if ($approvedDonationCount >= $t) {
                $this->ensureBadge($userId, $name);
            }
        }
    }

    private function ensureBadge(int $userId, string $badgeName): void
    {
        $name = trim($badgeName);
        if ($name === '') {
            return;
        }

        Badge::query()->firstOrCreate(
            ['user_id' => $userId, 'badge_name' => $name],
            ['user_id' => $userId, 'badge_name' => $name],
        );
    }
}

