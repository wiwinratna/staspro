<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasColumn('request_pembelian_header', 'keterangan_reject')) {
            Schema::table('request_pembelian_header', function (Blueprint $table) {
                $table->text('keterangan_reject')->nullable()->after('status_request');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('request_pembelian_header', 'keterangan_reject')) {
            Schema::table('request_pembelian_header', function (Blueprint $table) {
                $table->dropColumn('keterangan_reject');
            });
        }
    }
};
