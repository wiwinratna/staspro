<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('request_pembelian_detail', 'invoice_pembelian')) {
            Schema::table('request_pembelian_detail', function (Blueprint $table) {
                $table->string('invoice_pembelian')->nullable()->after('bukti_bayar');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('request_pembelian_detail', 'invoice_pembelian')) {
            Schema::table('request_pembelian_detail', function (Blueprint $table) {
                $table->dropColumn('invoice_pembelian');
            });
        }
    }
};
