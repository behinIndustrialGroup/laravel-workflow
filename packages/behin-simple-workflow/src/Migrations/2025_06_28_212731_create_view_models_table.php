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
        Schema::create('wf_view_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('api_key');
            $table->string('entity_id');
            $table->string('entity_name');
            $table->integer('max_number_of_rows')->nullable();
            $table->string('default_fields');
            $table->enum('show_as', ['table', 'box']);
            $table->tinyInteger('allow_create_row')->default(0);
            $table->string('create_form')->nullable();
            $table->string('create_form_fields')->nullable();
            $table->tinyInteger('show_create_form_at_the_end')->default(0);
            $table->tinyInteger('allow_update_row')->default(0);
            $table->string('update_form')->nullable();
            $table->string('update_form_fields')->nullable();
            $table->string('which_rows_user_can_update')->nullable();
            $table->tinyInteger('allow_delete_row')->default(0);
            $table->string('which_rows_user_can_delete')->nullable();
            $table->tinyInteger('allow_read_row')->default(0);
            $table->string('read_form')->nullable();
            $table->string('read_form_fields')->nullable();
            $table->string('which_rows_user_can_read')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wf_view_models');
    }
};
