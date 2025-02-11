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
        Schema::table('wf_entities', function (Blueprint $table) {
            $table->string('model_name')->nullable();
            $table->string('namespace')->nullable();
            $table->string('db_table_name')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wf_entities', function (Blueprint $table) {
            $table->dropColumn('model_name');
            $table->dropColumn('namespace');
            $table->dropColumn('db_table_name');
        });
    }
};
