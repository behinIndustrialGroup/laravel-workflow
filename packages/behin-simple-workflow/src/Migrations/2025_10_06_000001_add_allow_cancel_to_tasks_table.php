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
        Schema::table('wf_task', function (Blueprint $table) {
            if (!Schema::hasColumn('wf_task', 'allow_cancel')) {
                $table->boolean('allow_cancel')->default(false)->after('script_before_open');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wf_task', function (Blueprint $table) {
            if (Schema::hasColumn('wf_task', 'allow_cancel')) {
                $table->dropColumn('allow_cancel');
            }
        });
    }
};
