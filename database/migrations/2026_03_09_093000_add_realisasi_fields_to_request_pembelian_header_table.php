<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_pembelian_header', function (Blueprint $table) {
            if (!Schema::hasColumn('request_pembelian_header', 'biaya_admin_transfer')) {
                $table->decimal('biaya_admin_transfer', 15, 2)->nullable()->default(0)->after('keterangan_reject');
            }
            if (!Schema::hasColumn('request_pembelian_header', 'nominal_final_total')) {
                $table->decimal('nominal_final_total', 15, 2)->nullable()->after('biaya_admin_transfer');
            }
            if (!Schema::hasColumn('request_pembelian_header', 'nominal_penambahan')) {
                $table->decimal('nominal_penambahan', 15, 2)->nullable()->default(0)->after('nominal_final_total');
            }
            if (!Schema::hasColumn('request_pembelian_header', 'nominal_pengurangan')) {
                $table->decimal('nominal_pengurangan', 15, 2)->nullable()->default(0)->after('nominal_penambahan');
            }
            if (!Schema::hasColumn('request_pembelian_header', 'keterangan_penambahan')) {
                $table->text('keterangan_penambahan')->nullable()->after('nominal_pengurangan');
            }
            if (!Schema::hasColumn('request_pembelian_header', 'keterangan_pengurangan')) {
                $table->text('keterangan_pengurangan')->nullable()->after('keterangan_penambahan');
            }
            if (!Schema::hasColumn('request_pembelian_header', 'bukti_transfer')) {
                $table->string('bukti_transfer')->nullable()->after('keterangan_pengurangan');
            }
            if (!Schema::hasColumn('request_pembelian_header', 'invoice_pembelian')) {
                $table->string('invoice_pembelian')->nullable()->after('bukti_transfer');
            }
        });
    }

    public function down(): void
    {
        $columns = [
            'biaya_admin_transfer',
            'nominal_final_total',
            'nominal_penambahan',
            'nominal_pengurangan',
            'keterangan_penambahan',
            'keterangan_pengurangan',
            'bukti_transfer',
            'invoice_pembelian',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('request_pembelian_header', $column)) {
                Schema::table('request_pembelian_header', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
