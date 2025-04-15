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
        Schema::dropIfExists('request_pembelian_detail');
        Schema::create('request_pembelian_detail', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->double('kuantitas');
            $table->double('harga');
            $table->string('link_pembelian');
            $table->string('bukti_bayar')->nullable();
            $table->unsignedBigInteger('id_request_pembelian_header');
            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated');
            $table->timestamps();

            $table->foreign('id_request_pembelian_header')->references('id')->on('request_pembelian_header');
            $table->foreign('user_id_created')->references('id')->on('users');
            $table->foreign('user_id_updated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_pembelian_detail', function (Blueprint $table) {
            $table->dropForeign(['id_request_pembelian_header']);
            $table->dropForeign(['user_id_created']);
            $table->dropForeign(['user_id_updated']);
        });
        Schema::dropIfExists('request_pembelian_detail');
    }
};
