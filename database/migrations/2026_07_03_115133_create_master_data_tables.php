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
        Schema::create('jenis_project', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('kode', 20)->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('jenis_pendanaan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('kode', 20)->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('provider_pendanaan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->string('singkatan', 50)->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('komponen_biaya', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('kode', 20)->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('skema_pendanaan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20);
            $table->string('nama', 255);
            $table->unsignedBigInteger('jenis_project_id');
            $table->unsignedBigInteger('jenis_pendanaan_id');
            $table->unsignedBigInteger('provider_id');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('jenis_project_id')->references('id')->on('jenis_project');
            $table->foreign('jenis_pendanaan_id')->references('id')->on('jenis_pendanaan');
            $table->foreign('provider_id')->references('id')->on('provider_pendanaan');
        });

        Schema::create('skema_komponen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('skema_pendanaan_id');
            $table->unsignedBigInteger('komponen_biaya_id');
            $table->unsignedTinyInteger('urutan')->default(0);
            $table->boolean('is_wajib')->default(false);
            $table->timestamps();

            $table->unique(['skema_pendanaan_id', 'komponen_biaya_id'], 'uq_skema_komponen');
            $table->foreign('skema_pendanaan_id')->references('id')->on('skema_pendanaan')->onDelete('cascade');
            $table->foreign('komponen_biaya_id')->references('id')->on('komponen_biaya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skema_komponen');
        Schema::dropIfExists('skema_pendanaan');
        Schema::dropIfExists('komponen_biaya');
        Schema::dropIfExists('provider_pendanaan');
        Schema::dropIfExists('jenis_pendanaan');
        Schema::dropIfExists('jenis_project');
    }
};
