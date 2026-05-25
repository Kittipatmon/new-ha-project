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
        Schema::table('recruitment_job_posts', function (Blueprint $table) {
            $table->string('position_name')->after('job_position_id')->nullable();
            $table->unsignedBigInteger('job_position_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitment_job_posts', function (Blueprint $table) {
            $table->dropColumn('position_name');
            $table->unsignedBigInteger('job_position_id')->nullable(false)->change();
        });
    }
};
