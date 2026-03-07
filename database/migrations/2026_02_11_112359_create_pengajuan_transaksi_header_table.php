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
        Schema::create('pengajuan_transaksi_header', function (Blueprint $table) {
            $table->id();
            $table->string('no_request')->unique();

            $table->unsignedBigInteger('id_project');
            $table->unsignedBigInteger('id_subkategori_sumberdana');

            $table->enum('tipe', ['pengajuan', 'reimbursement']);
            $table->enum('jenis_transaksi', ['pemasukan', 'pengeluaran'])->default('pengeluaran');

            $table->text('deskripsi')->nullable();
            $table->decimal('estimasi_nominal', 18, 2)->default(0);
            $table->date('tgl_request')->nullable();

            // rekening tujuan transfer
            $table->string('nama_bank')->nullable();
            $table->string('no_rekening')->nullable();

            // approve / pencairan
            $table->date('tgl_cair')->nullable();
            $table->decimal('nominal_disetujui', 18, 2)->nullable();
            $table->string('metode_pembayaran')->nullable();

            // bukti
            $table->date('tgl_bukti')->nullable();
            $table->decimal('nominal_realisasi', 18, 2)->nullable();
            $table->string('bukti_file')->nullable();

            $table->enum('status', ['submit', 'approve', 'reject', 'bukti', 'done'])->default('submit');
            $table->text('keterangan_reject')->nullable();

            $table->unsignedBigInteger('pencatatan_keuangan_id')->nullable();

            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated')->nullable();

            $table->timestamps();

            $table->index(['id_project', 'status']);
            $table->index(['tipe', 'status']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_transaksi_header');
    }
};
