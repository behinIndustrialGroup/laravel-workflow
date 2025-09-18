<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wf_entity_petty_cashes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->bigInteger('amount');
            $table->unsignedBigInteger('paid_at');
            $table->string('from_account')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wf_entity_petty_cashes');
    }
};
