<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('detail_subkategori', function (Blueprint $table) {
            $table->unsignedBigInteger('anggaran_revisi')->nullable()->after('nominal');
        });
    }

    public function down(): void
    {
        Schema::table('detail_subkategori', function (Blueprint $table) {
            $table->dropColumn('anggaran_revisi');
        });
    }
};
