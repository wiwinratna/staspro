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
        Schema::dropIfExists('request_pembelian_header');
        Schema::create('request_pembelian_header', function (Blueprint $table) {
            $table->id();
            $table->string('no_request')->unique();
            $table->date('tgl_request');
            $table->enum('status_request', ['submit_request', 'approve_admin', 'submit_payment', 'approve_payment', 'done'])->default('submit_request');
            $table->unsignedBigInteger('id_project');
            $table->unsignedBigInteger('user_id_created');
            $table->unsignedBigInteger('user_id_updated');
            $table->timestamps();

            $table->foreign('id_project')->references('id')->on('project');
            $table->foreign('user_id_created')->references('id')->on('users');
            $table->foreign('user_id_updated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_pembelian_header', function (Blueprint $table) {
            $table->dropForeign(['id_project']);
            $table->dropForeign(['user_id_created']);
            $table->dropForeign(['user_id_updated']);
        });
        Schema::dropIfExists('request_pembelian_header');
    }
};
