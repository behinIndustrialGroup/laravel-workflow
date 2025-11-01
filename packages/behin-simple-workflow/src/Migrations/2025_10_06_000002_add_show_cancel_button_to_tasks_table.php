<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;
use Illuminate\\Support\\Facades\\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wf_task', function (Blueprint $table) {
            if (!Schema::hasColumn('wf_task', 'show_cancel_button')) {
                $table->boolean('show_cancel_button')->default(true)->after('allow_cancel');
            }
        });

        DB::table('wf_task')->whereNull('show_cancel_button')->update(['show_cancel_button' => true]);
    }

    public function down(): void
    {
        Schema::table('wf_task', function (Blueprint $table) {
            if (Schema::hasColumn('wf_task', 'show_cancel_button')) {
                $table->dropColumn('show_cancel_button');
            }
        });
    }
};
