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
        Schema::create('subkategori_sumberdana', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nama_form');
            $table->unsignedBigInteger('id_sumberdana');
            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated');
            $table->timestamps();

            $table->foreign('id_sumberdana')->references('id')->on('sumber_dana');
            $table->foreign('user_id_created')->references('id')->on('users');
            $table->foreign('user_id_updated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subkategori_sumberdana', function (Blueprint $table) {
            $table->dropForeign(['id_sumberdana']);
            $table->dropForeign(['user_id_created']);
            $table->dropForeign(['user_id_updated']);
        });
        Schema::dropIfExists('subkategori_sumberdana');
    }
};
