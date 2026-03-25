<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();

            $table->string('app_name')->default('Blood Donation');
            $table->string('app_tagline')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('primary_color_hex', 16)->default('#B71C1C');
            $table->string('secondary_color_hex', 16)->nullable();

            $table->boolean('welcome_enabled')->default(true);
            $table->string('welcome_title')->nullable();
            $table->text('welcome_message')->nullable();
            $table->string('welcome_button_text')->default('Got it');
            $table->boolean('welcome_show_once')->default(true);

            $table->boolean('feature_chat_enabled')->default(true);
            $table->boolean('feature_referrals_enabled')->default(true);
            $table->boolean('feature_wallet_enabled')->default(true);
            $table->boolean('feature_redeem_enabled')->default(false);

            $table->boolean('auth_require_phone_verification')->default(true);
            $table->unsignedTinyInteger('auth_min_phone_digits')->default(10);

            $table->text('firebase_options_json')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
