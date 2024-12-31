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
        $startNumber = config('workflow.caseStartValue') ? config('workflow.caseStartValue') : 1000;
        Schema::create('wf_cases', function (Blueprint $table) use($startNumber){
            $table->uuid('id')->primary();
            $table->uuid('process_id');
            $table->bigInteger('number')->startingValue($startNumber);
            $table->string('name')->nullable();
            $table->foreignId('creator')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('process_id')->references('id')->on('wf_process');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wf_cases');
    }
};
