<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('blood_group');
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->string('hospital');
            $table->text('message')->nullable();
            $table->string('status')->default('open'); // open | closed | fulfilled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
