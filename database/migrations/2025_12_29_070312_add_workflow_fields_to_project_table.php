<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('project', 'workflow_status')) {
            Schema::table('project', function (Blueprint $table) {
                $table->enum('workflow_status', ['submitted', 'approved', 'rejected', 'funded', 'finalized'])
                    ->default('submitted');
            });
        }

        $ketuaAfter = null;
        if (Schema::hasColumn('project', 'workflow_status')) {
            $ketuaAfter = 'workflow_status';
        } elseif (Schema::hasColumn('project', 'status')) {
            $ketuaAfter = 'status';
        }

        if (!Schema::hasColumn('project', 'ketua_id')) {
            Schema::table('project', function (Blueprint $table) use ($ketuaAfter) {
                $column = $table->unsignedBigInteger('ketua_id')->nullable();
                if ($ketuaAfter !== null) {
                    $column->after($ketuaAfter);
                }
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
        foreach (['ketua_id', 'submitted_at', 'approved_at', 'funded_at', 'finalized_at', 'workflow_status'] as $col) {
            if (Schema::hasColumn('project', $col)) {
                Schema::table('project', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }
    }
};
