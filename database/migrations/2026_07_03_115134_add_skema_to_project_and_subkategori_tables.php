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
        Schema::table('project', function (Blueprint $table) {
            $table->unsignedBigInteger('skema_pendanaan_id')->nullable()->after('id_sumber_dana');
            $table->foreign('skema_pendanaan_id', 'fk_project_skema')->references('id')->on('skema_pendanaan')->onDelete('set null');
        });

        Schema::table('subkategori_sumberdana', function (Blueprint $table) {
            $table->unsignedBigInteger('komponen_biaya_id')->nullable()->after('id_sumberdana');
            $table->foreign('komponen_biaya_id', 'fk_subkategori_komponen')->references('id')->on('komponen_biaya')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subkategori_sumberdana', function (Blueprint $table) {
            $table->dropForeign('fk_subkategori_komponen');
            $table->dropColumn('komponen_biaya_id');
        });

        Schema::table('project', function (Blueprint $table) {
            $table->dropForeign('fk_project_skema');
            $table->dropColumn('skema_pendanaan_id');
        });
    }
};
