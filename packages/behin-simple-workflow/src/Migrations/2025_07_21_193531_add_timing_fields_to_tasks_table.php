<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wf_task', function (Blueprint $table) {
            $table->enum('timing_type', ['static', 'dynamic'])->nullable();
            $table->string('timing_value')->nullable();
            $table->string('timing_key_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wf_task', function (Blueprint $table) {
            $table->dropColumn('timing_type');
            $table->dropColumn('timing_value');
            $table->dropColumn('timing_key_name');
        });
    }
};
