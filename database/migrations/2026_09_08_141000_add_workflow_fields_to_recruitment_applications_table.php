<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('recruitment_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('recruitment_applications', 'onboarding_date')) {
                $table->date('onboarding_date')->nullable()->after('final_result_at')->comment('กำหนดวันเริ่มงาน');
            }
            if (!Schema::hasColumn('recruitment_applications', 'dept_reviewed_by')) {
                $table->unsignedBigInteger('dept_reviewed_by')->nullable()->after('screened_at')->comment('ID หัวหน้าแผนกที่พิจารณา');
            }
            if (!Schema::hasColumn('recruitment_applications', 'dept_reviewed_at')) {
                $table->timestamp('dept_reviewed_at')->nullable()->after('dept_reviewed_by')->comment('วันเวลาที่หัวหน้าแผนกพิจารณา');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitment_applications', function (Blueprint $table) {
            $table->dropColumn(['onboarding_date', 'dept_reviewed_by', 'dept_reviewed_at']);
        });
    }
};
