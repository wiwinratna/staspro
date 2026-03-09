<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'jurusan')) {
                $table->string('jurusan', 120)->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'fakultas')) {
                $table->string('fakultas', 120)->nullable()->after('jurusan');
            }
            if (!Schema::hasColumn('users', 'nim_nip')) {
                $table->string('nim_nip', 50)->nullable()->after('fakultas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nim_nip')) {
                $table->dropColumn('nim_nip');
            }
            if (Schema::hasColumn('users', 'fakultas')) {
                $table->dropColumn('fakultas');
            }
            if (Schema::hasColumn('users', 'jurusan')) {
                $table->dropColumn('jurusan');
            }
            if (Schema::hasColumn('users', 'profile_photo')) {
                $table->dropColumn('profile_photo');
            }
        });
    }
};

