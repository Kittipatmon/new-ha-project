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
        Schema::table('recruitment_job_posts', function (Blueprint $table) {
            if (!Schema::hasColumn('recruitment_job_posts', 'views')) {
                $table->unsignedBigInteger('views')->default(0)->after('publish_status')->comment('จำนวนครั้งที่กดดูประกาศ');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitment_job_posts', function (Blueprint $table) {
            if (Schema::hasColumn('recruitment_job_posts', 'views')) {
                $table->dropColumn('views');
            }
        });
    }
};
