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
        Schema::table('transaksi', function (Blueprint $table) {
            $table->unsignedBigInteger('request_pembelian_id')->nullable()->after('bukti_transaksi');

            // Jika Anda ingin menambahkan foreign key constraint
            $table->foreign('request_pembelian_id')->references('id')->on('request_pembelian_header')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['request_pembelian_id']); // Hapus foreign key constraint jika ada
            $table->dropColumn('request_pembelian_id');
        });
    }
};
