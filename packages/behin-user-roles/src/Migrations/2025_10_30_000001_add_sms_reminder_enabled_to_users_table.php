<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'sms_reminder_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('sms_reminder_enabled')->default(true)->after('valid_ip');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'sms_reminder_enabled')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('sms_reminder_enabled');
            });
        }
    }
};
