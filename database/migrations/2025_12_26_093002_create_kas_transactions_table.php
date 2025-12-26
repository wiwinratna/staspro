<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kas_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('tipe', ['masuk', 'keluar']); // cash in/out
            $table->string('kategori'); // sisa_project, denda, donasi, dll
            $table->unsignedBigInteger('project_id')->nullable();
            $table->decimal('nominal', 12, 2);
            $table->text('deskripsi')->nullable();
            $table->string('metode_pembayaran')->nullable(); // cash/transfer
            $table->string('bukti')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('project')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['tanggal', 'tipe']);
            $table->index(['project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas_transactions');
    }
};
