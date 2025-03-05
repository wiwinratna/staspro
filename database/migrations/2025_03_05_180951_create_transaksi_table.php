<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->index();
            $table->string('jenis_transaksi');
            $table->string('deskripsi_transaksi');
            $table->decimal('jumlah_transaksi', 15, 2); // Pakai decimal agar lebih presisi
            $table->string('metode_pembayaran');
            $table->string('kategori_transaksi');
            $table->string('sub_kategori')->nullable();
            $table->string('sub_sub_kategori')->nullable();
            $table->string('bukti_transaksi')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi'); // Sesuaikan nama tabel jika perlu
    }
};
