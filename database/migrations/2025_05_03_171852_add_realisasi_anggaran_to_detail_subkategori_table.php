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
        Schema::table('detail_subkategori', function (Blueprint $table) {
            $table->decimal('realisasi_anggaran', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    
    public function down(): void
    {
        Schema::table('detail_subkategori', function (Blueprint $table) {
            $table->dropColumn('realisasi_anggaran');
        });
    }
};
