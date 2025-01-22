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
        Schema::create('wf_inbox', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('task_id');
            $table->uuid('case_id');
            $table->foreignId('actor')->constrained('users');
            $table->enum('status', ['new', 'opened', 'inProgress', 'done', 'canceled', 'draft', 'doneByOther'])->default('new');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('task_id')->references('id')->on('wf_task')->onDelete('cascade');
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
        Schema::dropIfExists('wf_inbox');
    }
};
