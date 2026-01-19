<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_funding', function (Blueprint $table) {
            $table->string('bukti')->nullable()->after('keterangan');
            $table->string('metode_penerimaan', 80)->nullable()->after('nominal'); // transfer/tunai/dll
        });
    }

    public function down(): void
    {
        Schema::table('project_funding', function (Blueprint $table) {
            $table->dropColumn(['bukti','metode_penerimaan']);
        });
    }
};
