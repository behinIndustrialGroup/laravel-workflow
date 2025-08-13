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
        Schema::table('wf_task_actor', function (Blueprint $table) {
            $table->string('actor')->nullable()->change();
            $table->unsignedBigInteger('role_id')->nullable()->after('actor');
            $table->foreign('role_id')->references('id')->on('behin_roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wf_task_actor', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->string('actor')->change();
        });
    }
};
