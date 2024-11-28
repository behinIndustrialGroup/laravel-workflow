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
        Schema::create('wf_task_actor', function (Blueprint $table) {
            $table->id();
            $table->uuid('task_id');
            $table->string('actor');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('task_id')->references('id')->on('wf_task');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wf_task_actor');
    }
};
