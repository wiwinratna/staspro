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
        Schema::table('request_pembelian_header', function (Blueprint $blueprint) {
            $blueprint->string('status_request', 50)->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_pembelian_header', function (Blueprint $blueprint) {
            // Kita kembalikan ke ENUM jika rollback
            $blueprint->enum('status_request', [
                'submit_request',
                'approve_request',
                'reject_request',
                'submit_payment',
                'approve_payment',
                'reject_payment',
                'done'
            ])->default('submit_request')->change();
        });
    }
};
