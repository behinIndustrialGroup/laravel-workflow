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
        Schema::create('wf_variables', function (Blueprint $table) {
            $table->id();
            $table->uuid('process_id');
            $table->uuid('case_id');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('process_id')->references('id')->on('wf_process')->onDelete('cascade');
            $table->foreign('case_id')->references('id')->on('wf_cases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wf_variables');
    }
};
