<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('wf_entity_counter_parties', 'user_id')) {
            Schema::table('wf_entity_counter_parties', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('state');
                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('wf_entity_counter_parties', 'user_id')) {
            Schema::table('wf_entity_counter_parties', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};
