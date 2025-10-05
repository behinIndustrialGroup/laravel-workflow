<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wf_entity_counter_parties', function (Blueprint $table) {
            if (!$table->hasColumn('user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('state');
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('wf_entity_counter_parties', function (Blueprint $table) {
            if ($table->hasColumn('user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
