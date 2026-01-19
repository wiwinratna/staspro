<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // workflow_status SUDAH ADA -> jangan ditambah lagi
        // Tambah kolom lain hanya jika belum ada

        if (!Schema::hasColumn('project', 'ketua_id')) {
            Schema::table('project', function (Blueprint $table) {
                $table->unsignedBigInteger('ketua_id')->nullable()->after('workflow_status');
            });
        }

        if (!Schema::hasColumn('project', 'submitted_at')) {
            Schema::table('project', function (Blueprint $table) {
                $table->timestamp('submitted_at')->nullable();
            });
        }

        if (!Schema::hasColumn('project', 'approved_at')) {
            Schema::table('project', function (Blueprint $table) {
                $table->timestamp('approved_at')->nullable();
            });
        }

        if (!Schema::hasColumn('project', 'funded_at')) {
            Schema::table('project', function (Blueprint $table) {
                $table->timestamp('funded_at')->nullable();
            });
        }

        if (!Schema::hasColumn('project', 'finalized_at')) {
            Schema::table('project', function (Blueprint $table) {
                $table->timestamp('finalized_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Drop kolom kalau ada (aman)
        Schema::table('project', function (Blueprint $table) {
            $drops = [];

            foreach (['ketua_id','submitted_at','approved_at','funded_at','finalized_at'] as $col) {
                if (Schema::hasColumn('project', $col)) $drops[] = $col;
            }

            if (!empty($drops)) $table->dropColumn($drops);
        });
    }
};
