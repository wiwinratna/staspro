<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->unsignedBigInteger('request_pembelian_id')->nullable()->after('id');

            // Optional: tambahkan foreign key jika kamu mau relasi DB-nya lebih ketat
            $table->foreign('request_pembelian_id')
                  ->references('id')
                  ->on('request_pembelian_header')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['request_pembelian_id']);
            $table->dropColumn('request_pembelian_id');
        });
    }
};
