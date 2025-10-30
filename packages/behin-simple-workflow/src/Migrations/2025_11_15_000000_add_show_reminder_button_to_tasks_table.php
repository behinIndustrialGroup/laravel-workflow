<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wf_task', function (Blueprint $table) {
            if (!Schema::hasColumn('wf_task', 'show_reminder_button')) {
                $table->boolean('show_reminder_button')->default(false)->after('show_save_button');
            }
        });

        DB::table('wf_task')->whereNull('show_reminder_button')->update(['show_reminder_button' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wf_task', function (Blueprint $table) {
            if (Schema::hasColumn('wf_task', 'show_reminder_button')) {
                $table->dropColumn('show_reminder_button');
            }
        });
    }
};
