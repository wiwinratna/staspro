<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom tipe_project pada tabel sumber_dana dan project.
     * Default 'Penelitian' agar data lama tetap aman.
     */
    public function up(): void
    {
        Schema::table('sumber_dana', function (Blueprint $table) {
            $table->enum('tipe_project', ['Penelitian', 'Abdimas'])
                  ->default('Penelitian')
                  ->after('id');
        });

        Schema::table('project', function (Blueprint $table) {
            $table->enum('tipe_project', ['Penelitian', 'Abdimas'])
                  ->default('Penelitian')
                  ->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sumber_dana', function (Blueprint $table) {
            $table->dropColumn('tipe_project');
        });

        Schema::table('project', function (Blueprint $table) {
            $table->dropColumn('tipe_project');
        });
    }
};
