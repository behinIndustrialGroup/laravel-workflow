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
            $table->string('color')->nullable();
            $table->string('background')->nullable();
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
            $table->dropColumn('color');
            $table->dropColumn('background');
        });
    }
};
