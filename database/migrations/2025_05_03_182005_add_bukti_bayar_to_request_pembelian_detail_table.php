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
        Schema::table('request_pembelian_detail', function (Blueprint $table) {
            $table->string('bukti_bayar')->nullable(); // Menambahkan kolom bukti_bayar
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('request_pembelian_detail', function (Blueprint $table) {
            $table->dropColumn('bukti_bayar'); // Menghapus kolom bukti_bayar
        });
    }
};
