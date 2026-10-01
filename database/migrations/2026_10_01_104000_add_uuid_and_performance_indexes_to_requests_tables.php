<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Manpower Requests
        if (Schema::hasTable('manpower_requests')) {
            Schema::table('manpower_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('manpower_requests', 'uuid')) {
                    $table->uuid('uuid')->nullable()->unique()->after('id')->comment('Public Sequential UUID สำหรับ URL และ API');
                }
            });

            // Populate UUID for existing records
            $existingMp = DB::table('manpower_requests')->whereNull('uuid')->select('id')->get();
            foreach ($existingMp as $row) {
                DB::table('manpower_requests')->where('id', $row->id)->update([
                    'uuid' => (string) Str::orderedUuid()
                ]);
            }

            // Performance indexes
            $existingIndexes = collect(Schema::getIndexes('manpower_requests'))->pluck('name')->toArray();
            Schema::table('manpower_requests', function (Blueprint $table) use ($existingIndexes) {
                if (!in_array('manpower_requests_status_updated_at_index', $existingIndexes)) {
                    $table->index(['status', 'updated_at'], 'manpower_requests_status_updated_at_index');
                }
                if (Schema::hasColumn('manpower_requests', 'deleted_at') && !in_array('manpower_requests_deleted_at_index', $existingIndexes)) {
                    $table->index('deleted_at', 'manpower_requests_deleted_at_index');
                }
            });
        }

        // 2. Probation Evaluations
        if (Schema::hasTable('probation_evaluations')) {
            Schema::table('probation_evaluations', function (Blueprint $table) {
                if (!Schema::hasColumn('probation_evaluations', 'uuid')) {
                    $table->uuid('uuid')->nullable()->unique()->after('id')->comment('Public Sequential UUID สำหรับ URL และ API');
                }
            });

            $existingProb = DB::table('probation_evaluations')->whereNull('uuid')->select('id')->get();
            foreach ($existingProb as $row) {
                DB::table('probation_evaluations')->where('id', $row->id)->update([
                    'uuid' => (string) Str::orderedUuid()
                ]);
            }

            $existingIndexes = collect(Schema::getIndexes('probation_evaluations'))->pluck('name')->toArray();
            Schema::table('probation_evaluations', function (Blueprint $table) use ($existingIndexes) {
                if (!in_array('probation_evaluations_status_updated_at_index', $existingIndexes)) {
                    $table->index(['status', 'updated_at'], 'probation_evaluations_status_updated_at_index');
                }
                if (Schema::hasColumn('probation_evaluations', 'deleted_at') && !in_array('probation_evaluations_deleted_at_index', $existingIndexes)) {
                    $table->index('deleted_at', 'probation_evaluations_deleted_at_index');
                }
            });
        }

        // 3. Interview Evaluations
        if (Schema::hasTable('interview_evaluations')) {
            Schema::table('interview_evaluations', function (Blueprint $table) {
                if (!Schema::hasColumn('interview_evaluations', 'uuid')) {
                    $table->uuid('uuid')->nullable()->unique()->after('id')->comment('Public Sequential UUID สำหรับ URL และ API');
                }
            });

            $existingIv = DB::table('interview_evaluations')->whereNull('uuid')->select('id')->get();
            foreach ($existingIv as $row) {
                DB::table('interview_evaluations')->where('id', $row->id)->update([
                    'uuid' => (string) Str::orderedUuid()
                ]);
            }

            $existingIndexes = collect(Schema::getIndexes('interview_evaluations'))->pluck('name')->toArray();
            Schema::table('interview_evaluations', function (Blueprint $table) use ($existingIndexes) {
                if (Schema::hasColumn('interview_evaluations', 'status') && !in_array('interview_evaluations_status_index', $existingIndexes)) {
                    $table->index('status', 'interview_evaluations_status_index');
                }
                if (Schema::hasColumn('interview_evaluations', 'deleted_at') && !in_array('interview_evaluations_deleted_at_index', $existingIndexes)) {
                    $table->index('deleted_at', 'interview_evaluations_deleted_at_index');
                }
            });
        }

        // 4. HR Requests
        if (Schema::hasTable('hr_requests')) {
            Schema::table('hr_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('hr_requests', 'uuid')) {
                    $table->uuid('uuid')->nullable()->unique()->after('hr_request_id')->comment('Public Sequential UUID สำหรับ URL และ API');
                }
            });

            $existingHr = DB::table('hr_requests')->whereNull('uuid')->select('hr_request_id')->get();
            foreach ($existingHr as $row) {
                DB::table('hr_requests')->where('hr_request_id', $row->hr_request_id)->update([
                    'uuid' => (string) Str::orderedUuid()
                ]);
            }

            $existingIndexes = collect(Schema::getIndexes('hr_requests'))->pluck('name')->toArray();
            Schema::table('hr_requests', function (Blueprint $table) use ($existingIndexes) {
                if (!in_array('hr_requests_status_updated_at_index', $existingIndexes)) {
                    $table->index(['status', 'updated_at'], 'hr_requests_status_updated_at_index');
                }
                if (Schema::hasColumn('hr_requests', 'deleted_at') && !in_array('hr_requests_deleted_at_index', $existingIndexes)) {
                    $table->index('deleted_at', 'hr_requests_deleted_at_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('manpower_requests')) {
            Schema::table('manpower_requests', function (Blueprint $table) {
                if (Schema::hasColumn('manpower_requests', 'uuid')) {
                    $table->dropColumn('uuid');
                }
            });
        }

        if (Schema::hasTable('probation_evaluations')) {
            Schema::table('probation_evaluations', function (Blueprint $table) {
                if (Schema::hasColumn('probation_evaluations', 'uuid')) {
                    $table->dropColumn('uuid');
                }
            });
        }

        if (Schema::hasTable('interview_evaluations')) {
            Schema::table('interview_evaluations', function (Blueprint $table) {
                if (Schema::hasColumn('interview_evaluations', 'uuid')) {
                    $table->dropColumn('uuid');
                }
            });
        }

        if (Schema::hasTable('hr_requests')) {
            Schema::table('hr_requests', function (Blueprint $table) {
                if (Schema::hasColumn('hr_requests', 'uuid')) {
                    $table->dropColumn('uuid');
                }
            });
        }
    }
};
