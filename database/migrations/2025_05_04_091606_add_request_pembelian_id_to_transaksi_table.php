<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasColumn('transaksi', 'request_pembelian_id')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->unsignedBigInteger('request_pembelian_id')->nullable()->after('bukti_transaksi');
            });
        }

        if ($this->foreignKeyExists('transaksi', 'transaksi_request_pembelian_id_foreign')) {
            return;
        }

        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreign('request_pembelian_id')
                ->references('id')
                ->on('request_pembelian_header')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if ($this->foreignKeyExists('transaksi', 'transaksi_request_pembelian_id_foreign')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->dropForeign('transaksi_request_pembelian_id_foreign');
            });
        }

        if (Schema::hasColumn('transaksi', 'request_pembelian_id')) {
            Schema::table('transaksi', function (Blueprint $table) {
                $table->dropColumn('request_pembelian_id');
            });
        }
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        $database = DB::getDatabaseName();

        $result = DB::selectOne(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = ?
               AND CONSTRAINT_TYPE = 'FOREIGN KEY'
               AND CONSTRAINT_NAME = ?
             LIMIT 1",
            [$database, $table, $constraint]
        );

        return $result !== null;
    }
};
