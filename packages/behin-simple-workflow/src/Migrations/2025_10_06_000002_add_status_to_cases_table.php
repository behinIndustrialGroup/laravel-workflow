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
        Schema::table('wf_cases', function (Blueprint $table) {
            if (!Schema::hasColumn('wf_cases', 'status')) {
                $table->string('status')->default('inProgress')->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wf_cases', function (Blueprint $table) {
            if (Schema::hasColumn('wf_cases', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
