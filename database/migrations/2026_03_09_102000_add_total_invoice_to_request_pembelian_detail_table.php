<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('request_pembelian_detail', 'total_invoice')) {
            Schema::table('request_pembelian_detail', function (Blueprint $table) {
                $table->decimal('total_invoice', 15, 2)->nullable()->after('invoice_pembelian');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('request_pembelian_detail', 'total_invoice')) {
            Schema::table('request_pembelian_detail', function (Blueprint $table) {
                $table->dropColumn('total_invoice');
            });
        }
    }
};
