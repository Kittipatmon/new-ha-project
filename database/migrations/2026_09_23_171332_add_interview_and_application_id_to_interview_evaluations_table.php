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
        Schema::table('interview_evaluations', function (Blueprint $table) {
            if (!Schema::hasColumn('interview_evaluations', 'interview_id')) {
                $table->unsignedBigInteger('interview_id')->nullable()->after('user_id')->index()->comment('ID การสัมภาษณ์ (recruitment_interviews)');
            }
            if (!Schema::hasColumn('interview_evaluations', 'application_id')) {
                $table->unsignedBigInteger('application_id')->nullable()->after('interview_id')->index()->comment('ID ใบสมัคร (recruitment_applications)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interview_evaluations', function (Blueprint $table) {
            if (Schema::hasColumn('interview_evaluations', 'interview_id')) {
                $table->dropColumn('interview_id');
            }
            if (Schema::hasColumn('interview_evaluations', 'application_id')) {
                $table->dropColumn('application_id');
            }
        });
    }
};
