<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_transaksi_header', function (Blueprint $table) {
            // Detail item
            if (!Schema::hasColumn('pengajuan_transaksi_header', 'kuantitas')) {
                $table->integer('kuantitas')->nullable()->after('deskripsi');
            }
            if (!Schema::hasColumn('pengajuan_transaksi_header', 'harga_satuan')) {
                $table->decimal('harga_satuan', 15, 2)->nullable()->after('kuantitas');
            }

            // Admin approval fields
            if (!Schema::hasColumn('pengajuan_transaksi_header', 'nominal_final')) {
                $table->decimal('nominal_final', 15, 2)->nullable()->after('nominal_disetujui');
            }
            if (!Schema::hasColumn('pengajuan_transaksi_header', 'biaya_admin')) {
                $table->decimal('biaya_admin', 15, 2)->default(0)->after('nominal_final');
            }

            // Talangan
            if (!Schema::hasColumn('pengajuan_transaksi_header', 'is_talangan')) {
                $table->boolean('is_talangan')->default(false)->after('biaya_admin');
            }
            if (!Schema::hasColumn('pengajuan_transaksi_header', 'status_alokasi')) {
                $table->string('status_alokasi', 20)->nullable()->after('is_talangan');
            }
            if (!Schema::hasColumn('pengajuan_transaksi_header', 'project_id_alokasi_final')) {
                $table->unsignedBigInteger('project_id_alokasi_final')->nullable()->after('status_alokasi');
            }
            if (!Schema::hasColumn('pengajuan_transaksi_header', 'tanggal_alokasi_final')) {
                $table->date('tanggal_alokasi_final')->nullable()->after('project_id_alokasi_final');
            }
            if (!Schema::hasColumn('pengajuan_transaksi_header', 'catatan_alokasi')) {
                $table->text('catatan_alokasi')->nullable()->after('tanggal_alokasi_final');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_transaksi_header', function (Blueprint $table) {
            $cols = [
                'kuantitas', 'harga_satuan', 'nominal_final', 'biaya_admin',
                'is_talangan', 'status_alokasi', 'project_id_alokasi_final',
                'tanggal_alokasi_final', 'catatan_alokasi',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('pengajuan_transaksi_header', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
