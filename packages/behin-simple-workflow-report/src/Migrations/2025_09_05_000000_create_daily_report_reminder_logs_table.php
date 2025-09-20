<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_report_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');
            $table->unsignedBigInteger('user_id');
            $table->string('mobile')->nullable();
            $table->string('status');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(['report_date', 'user_id'], 'daily_report_reminder_logs_report_date_user_id_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_report_reminder_logs');
    }
};
