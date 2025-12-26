<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('project', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'ditutup'])->default('aktif')->after('deskripsi');
            $table->timestamp('closed_at')->nullable()->after('status');
            $table->unsignedBigInteger('closed_by')->nullable()->after('closed_at');

            $table->foreign('closed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('project', function (Blueprint $table) {
            $table->dropForeign(['closed_by']);
            $table->dropColumn(['status', 'closed_at', 'closed_by']);
        });
    }
};
