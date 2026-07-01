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
            $table->string('nama_penerima')->nullable()->after('is_sampai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_pembelian_detail', function (Blueprint $table) {
            $table->dropColumn('nama_penerima');
        });
    }
};
