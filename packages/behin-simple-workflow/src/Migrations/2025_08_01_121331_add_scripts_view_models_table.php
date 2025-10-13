<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $columnsToAdd = [];

        if (!Schema::hasColumn('wf_view_models', 'script_after_create')) {
            $columnsToAdd[] = 'script_after_create';
        }

        if (!Schema::hasColumn('wf_view_models', 'script_before_create')) {
            $columnsToAdd[] = 'script_before_create';
        }

        if (!Schema::hasColumn('wf_view_models', 'script_after_update')) {
            $columnsToAdd[] = 'script_after_update';
        }

        if (!Schema::hasColumn('wf_view_models', 'script_after_delete')) {
            $columnsToAdd[] = 'script_after_delete';
        }

        if (!Schema::hasColumn('wf_view_models', 'script_before_show_rows')) {
            $columnsToAdd[] = 'script_before_show_rows';
        }

        if (!empty($columnsToAdd)) {
            Schema::table('wf_view_models', function (Blueprint $table) use ($columnsToAdd) {
                foreach ($columnsToAdd as $column) {
                    $table->string($column)->nullable();
                }
            });
        }
    }

    public function down()
    {
        Schema::table('wf_view_models', function (Blueprint $table) {
            $table->dropColumn([
                'script_after_create',
                'script_before_create',
                'script_after_update',
                'script_after_delete',
                'script_before_show_rows',
            ]);
        });
    }
};
