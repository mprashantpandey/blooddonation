<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->unsignedInteger('points_donation_default')->default(100);
            $table->unsignedInteger('points_referral_referrer')->default(50);
            $table->unsignedInteger('points_referral_new_user')->default(20);

            $table->unsignedTinyInteger('verified_after_approved_donations')->default(1);

            $table->unsignedTinyInteger('badge_donation_1_threshold')->default(1);
            $table->unsignedTinyInteger('badge_donation_3_threshold')->default(3);
            $table->unsignedTinyInteger('badge_donation_5_threshold')->default(5);
            $table->unsignedTinyInteger('badge_referral_threshold')->default(5);

            $table->string('badge_donation_1_name')->default('Life Saver');
            $table->string('badge_donation_3_name')->default('Hero Donor');
            $table->string('badge_donation_5_name')->default('Blood Champion');
            $table->string('badge_referral_name')->default('Community Builder');
            $table->string('badge_verified_name')->default('Verified');
        });
    }

    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn([
                'points_donation_default',
                'points_referral_referrer',
                'points_referral_new_user',
                'verified_after_approved_donations',
                'badge_donation_1_threshold',
                'badge_donation_3_threshold',
                'badge_donation_5_threshold',
                'badge_referral_threshold',
                'badge_donation_1_name',
                'badge_donation_3_name',
                'badge_donation_5_name',
                'badge_referral_name',
                'badge_verified_name',
            ]);
        });
    }
};

