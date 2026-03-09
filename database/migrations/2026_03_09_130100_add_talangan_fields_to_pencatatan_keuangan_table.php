<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pencatatan_keuangan', function (Blueprint $table) {
            if (!Schema::hasColumn('pencatatan_keuangan', 'is_talangan')) {
                $table->boolean('is_talangan')->default(false)->after('request_pembelian_id');
            }
            if (!Schema::hasColumn('pencatatan_keuangan', 'talangan_ref_type')) {
                $table->string('talangan_ref_type', 50)->nullable()->after('is_talangan');
            }
            if (!Schema::hasColumn('pencatatan_keuangan', 'talangan_ref_id')) {
                $table->unsignedBigInteger('talangan_ref_id')->nullable()->after('talangan_ref_type');
            }
            if (!Schema::hasColumn('pencatatan_keuangan', 'is_reclass')) {
                $table->boolean('is_reclass')->default(false)->after('talangan_ref_id');
            }
            if (!Schema::hasColumn('pencatatan_keuangan', 'reclass_group_id')) {
                $table->string('reclass_group_id', 64)->nullable()->after('is_reclass');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pencatatan_keuangan', function (Blueprint $table) {
            if (Schema::hasColumn('pencatatan_keuangan', 'reclass_group_id')) {
                $table->dropColumn('reclass_group_id');
            }
            if (Schema::hasColumn('pencatatan_keuangan', 'is_reclass')) {
                $table->dropColumn('is_reclass');
            }
            if (Schema::hasColumn('pencatatan_keuangan', 'talangan_ref_id')) {
                $table->dropColumn('talangan_ref_id');
            }
            if (Schema::hasColumn('pencatatan_keuangan', 'talangan_ref_type')) {
                $table->dropColumn('talangan_ref_type');
            }
            if (Schema::hasColumn('pencatatan_keuangan', 'is_talangan')) {
                $table->dropColumn('is_talangan');
            }
        });
    }
};

