<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('blood_group');
            $table->date('last_donation_date')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donors');
    }
};
