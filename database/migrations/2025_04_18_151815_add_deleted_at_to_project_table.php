<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('project', function (Blueprint $table) {
            $table->softDeletes(); // This will add the deleted_at column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('project', function (Blueprint $table) {
            $table->dropSoftDeletes(); // This will remove the deleted_at column
        });
    }
};
