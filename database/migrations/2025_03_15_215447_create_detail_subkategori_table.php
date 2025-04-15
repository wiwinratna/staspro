<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_subkategori', function (Blueprint $table) {
            $table->id();
            $table->double('nominal');
            $table->unsignedBigInteger('id_subkategori_sumberdana');
            $table->unsignedBigInteger('id_project');
            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated');
            $table->timestamps();

            $table->foreign('id_subkategori_sumberdana')->references('id')->on('subkategori_sumberdana');
            $table->foreign('id_project')->references('id')->on('project');
            $table->foreign('user_id_created')->references('id')->on('users');
            $table->foreign('user_id_updated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_subkategori', function (Blueprint $table) {
            $table->dropForeign(['id_subkategori_sumberdana']);
            $table->dropForeign(['id_project']);
            $table->dropForeign(['user_id_created']);
            $table->dropForeign(['user_id_updated']);
        });
        Schema::dropIfExists('detail_subkategori');
    }
};
