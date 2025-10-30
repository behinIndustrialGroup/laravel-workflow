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
            if (!Schema::hasColumn('wf_task', 'show_save_button')) {
                $table->boolean('show_save_button')->default(false)->after('is_preview');
            }
        });

        DB::table('wf_task')->whereNull('show_save_button')->update(['show_save_button' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wf_task', function (Blueprint $table) {
            if (Schema::hasColumn('wf_task', 'show_save_button')) {
                $table->dropColumn('show_save_button');
            }
        });
    }
};
