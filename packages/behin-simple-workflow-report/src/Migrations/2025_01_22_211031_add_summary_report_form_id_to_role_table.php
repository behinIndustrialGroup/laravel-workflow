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
        Schema::table('behin_roles', function (Blueprint $table) {
            $table->uuid('summary_report_form_id')->nullable()->after('name');
            $table->foreign('summary_report_form_id')->references('id')->on('wf_forms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('wf_task');
    }
};
