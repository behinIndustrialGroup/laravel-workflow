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
            if (!Schema::hasColumn('wf_task', 'is_preview')) {
                $table->boolean('is_preview')->default(false)->after('assignment_type');
            }
        });

        DB::table('wf_task')->whereNull('is_preview')->update(['is_preview' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wf_task', function (Blueprint $table) {
            if (Schema::hasColumn('wf_task', 'is_preview')) {
                $table->dropColumn('is_preview');
            }
        });
    }
};
