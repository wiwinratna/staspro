<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pencatatan_keuangan')) {
            return;
        }

        Schema::create('pencatatan_keuangan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('sub_kategori_pendanaan')->nullable();
            $table->enum('jenis_transaksi', ['pemasukan', 'pengeluaran']);
            $table->string('deskripsi_transaksi', 255);
            $table->decimal('jumlah_transaksi', 15, 2)->default(0);
            $table->string('metode_pembayaran', 100)->nullable();
            $table->string('bukti_transaksi')->nullable();
            $table->unsignedBigInteger('request_pembelian_id')->nullable();
            $table->timestamps();

            $table->foreign('project_id')
                ->references('id')
                ->on('project')
                ->nullOnDelete();

            $table->foreign('sub_kategori_pendanaan')
                ->references('id')
                ->on('subkategori_sumberdana')
                ->nullOnDelete();

            $table->foreign('request_pembelian_id')
                ->references('id')
                ->on('request_pembelian_header')
                ->nullOnDelete();

            $table->index(['tanggal', 'jenis_transaksi']);
            $table->index('project_id');
            $table->index('sub_kategori_pendanaan');
            $table->index('request_pembelian_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pencatatan_keuangan');
    }
};
