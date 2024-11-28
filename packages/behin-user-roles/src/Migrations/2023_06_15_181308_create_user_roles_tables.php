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
        Schema::create('behin_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('behin_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('disable')->default(0);
            $table->timestamps();
        });

        Schema::create('behin_access', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('role_id')->unsigned();
            $table->bigInteger('method_id')->unsigned();
            $table->tinyInteger('access')->default(0);
            $table->timestamps();
            $table->foreign('role_id')->references('id')->on('behin_roles');
            $table->foreign('method_id')->references('id')->on('behin_methods');
        });

        Schema::table('users', function(Blueprint $table){
            $table->bigInteger('role_id')->unsigned()->nullable();
            $table->foreign('role_id')->references('id')->on('behin_roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('behin_access');
        Schema::dropIfExists('behin_methods');
        Schema::dropIfExists('behin_roles');
        Schema::table('users', function(Blueprint $table){
            $table->dropColumn('role_id');
        });
    }
};
