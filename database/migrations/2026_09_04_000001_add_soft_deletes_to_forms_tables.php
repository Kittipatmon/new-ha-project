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
        if (Schema::hasTable('manpower_requests') && !Schema::hasColumn('manpower_requests', 'deleted_at')) {
            Schema::table('manpower_requests', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('probation_evaluations') && !Schema::hasColumn('probation_evaluations', 'deleted_at')) {
            Schema::table('probation_evaluations', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('interview_evaluations') && !Schema::hasColumn('interview_evaluations', 'deleted_at')) {
            Schema::table('interview_evaluations', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('hr_requests') && !Schema::hasColumn('hr_requests', 'deleted_at')) {
            Schema::table('hr_requests', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('manpower_requests') && Schema::hasColumn('manpower_requests', 'deleted_at')) {
            Schema::table('manpower_requests', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('probation_evaluations') && Schema::hasColumn('probation_evaluations', 'deleted_at')) {
            Schema::table('probation_evaluations', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('interview_evaluations') && Schema::hasColumn('interview_evaluations', 'deleted_at')) {
            Schema::table('interview_evaluations', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('hr_requests') && Schema::hasColumn('hr_requests', 'deleted_at')) {
            Schema::table('hr_requests', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
