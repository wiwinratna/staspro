<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_pembelian_detail', function (Blueprint $table) {
            if (!Schema::hasColumn('request_pembelian_detail', 'is_sampai')) {
                $table->boolean('is_sampai')->default(false)->after('total_invoice');
            }
            if (!Schema::hasColumn('request_pembelian_detail', 'is_pelaporan')) {
                $table->boolean('is_pelaporan')->default(false)->after('is_sampai');
            }
        });
    }

    public function down(): void
    {
        Schema::table('request_pembelian_detail', function (Blueprint $table) {
            if (Schema::hasColumn('request_pembelian_detail', 'is_pelaporan')) {
                $table->dropColumn('is_pelaporan');
            }
            if (Schema::hasColumn('request_pembelian_detail', 'is_sampai')) {
                $table->dropColumn('is_sampai');
            }
        });
    }
};

