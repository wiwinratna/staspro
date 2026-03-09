<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('request_pembelian_header', function (Blueprint $table) {
            if (!Schema::hasColumn('request_pembelian_header', 'is_talangan')) {
                $table->boolean('is_talangan')->default(false)->after('status_request');
            }
            if (!Schema::hasColumn('request_pembelian_header', 'status_alokasi')) {
                $table->string('status_alokasi', 20)->default('belum')->after('is_talangan');
            }
            if (!Schema::hasColumn('request_pembelian_header', 'project_id_alokasi_final')) {
                $table->unsignedBigInteger('project_id_alokasi_final')->nullable()->after('id_project');
            }
            if (!Schema::hasColumn('request_pembelian_header', 'tanggal_alokasi_final')) {
                $table->date('tanggal_alokasi_final')->nullable()->after('project_id_alokasi_final');
            }
            if (!Schema::hasColumn('request_pembelian_header', 'catatan_alokasi')) {
                $table->text('catatan_alokasi')->nullable()->after('tanggal_alokasi_final');
            }
        });
    }

    public function down(): void
    {
        Schema::table('request_pembelian_header', function (Blueprint $table) {
            if (Schema::hasColumn('request_pembelian_header', 'catatan_alokasi')) {
                $table->dropColumn('catatan_alokasi');
            }
            if (Schema::hasColumn('request_pembelian_header', 'tanggal_alokasi_final')) {
                $table->dropColumn('tanggal_alokasi_final');
            }
            if (Schema::hasColumn('request_pembelian_header', 'project_id_alokasi_final')) {
                $table->dropColumn('project_id_alokasi_final');
            }
            if (Schema::hasColumn('request_pembelian_header', 'status_alokasi')) {
                $table->dropColumn('status_alokasi');
            }
            if (Schema::hasColumn('request_pembelian_header', 'is_talangan')) {
                $table->dropColumn('is_talangan');
            }
        });
    }
};

