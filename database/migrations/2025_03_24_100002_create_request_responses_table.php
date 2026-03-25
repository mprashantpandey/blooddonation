<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('blood_requests')->cascadeOnDelete();
            $table->foreignId('donor_id')->constrained('donors')->cascadeOnDelete();
            $table->string('status'); // interested | ignored
            $table->timestamps();

            $table->unique(['request_id', 'donor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_responses');
    }
};
