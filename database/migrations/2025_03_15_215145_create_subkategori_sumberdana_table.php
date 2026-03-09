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
        Schema::create('subkategori_sumberdana', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nama_form');
            $table->foreignId('id_sumberdana')
                ->constrained('sumber_dana')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('user_id_created')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('user_id_updated')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subkategori_sumberdana');
    }
};
