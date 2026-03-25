<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // FCM tokens can be long; do NOT index the raw token on MySQL.
            $table->text('token');
            $table->char('token_hash', 64);
            $table->string('platform', 32)->nullable(); // android | ios | web | unknown
            $table->string('device_id', 191)->nullable(); // optional, for future unregistration
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'token_hash']);
            $table->index(['token_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_fcm_tokens');
    }
};

