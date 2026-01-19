<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_funding', function (Blueprint $table) {
            $table->id();

            // relasi project
            $table->unsignedBigInteger('project_id');

            // dana cair
            $table->date('tanggal');
            $table->unsignedBigInteger('nominal'); // rupiah
            $table->string('sumber_dana', 150)->nullable(); 
            $table->text('keterangan')->nullable();

            // audit
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            // foreign key
            $table->foreign('project_id')
                ->references('id')
                ->on('project')
                ->cascadeOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->index(['project_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_funding');
    }
};
