<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('donors')->cascadeOnDelete();
            $table->foreignId('request_id')->constrained('blood_requests')->cascadeOnDelete();
            $table->string('proof_image')->nullable();
            $table->string('hospital_name')->nullable();
            $table->string('status')->default('pending'); // pending | approved | rejected
            $table->unsignedInteger('points')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
