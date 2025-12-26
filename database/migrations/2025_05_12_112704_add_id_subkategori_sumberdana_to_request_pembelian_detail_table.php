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
        Schema::table('request_pembelian_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('id_subkategori_sumberdana')->nullable()->after('id_request_pembelian_header');
            $table->foreign('id_subkategori_sumberdana')->references('id')->on('subkategori_sumberdana')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_pembelian_detail', function (Blueprint $table) {
            $table->dropForeign(['id_subkategori_sumberdana']);
            $table->dropColumn('id_subkategori_sumberdana');
        });
    }
};
