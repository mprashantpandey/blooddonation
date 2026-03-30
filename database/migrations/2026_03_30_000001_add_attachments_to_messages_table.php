<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->text('message')->nullable()->change();
            $table->string('attachment_url')->nullable()->after('message');
            $table->string('attachment_mime')->nullable()->after('attachment_url');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['attachment_url', 'attachment_mime']);
            $table->text('message')->nullable(false)->change();
        });
    }
};

