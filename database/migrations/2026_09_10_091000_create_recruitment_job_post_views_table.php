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
        // Add clicks counter to recruitment_job_posts if not exists
        if (!Schema::hasColumn('recruitment_job_posts', 'clicks')) {
            Schema::table('recruitment_job_posts', function (Blueprint $table) {
                $table->unsignedBigInteger('clicks')->default(0)->after('views');
            });
        }

        // Detailed view & click tracking table for recruitment job posts
        Schema::create('recruitment_job_post_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained('recruitment_job_posts')->onDelete('cascade');
            $table->string('event_type')->default('view'); // 'view' or 'click'
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->date('view_date');
            $table->timestamps();

            $table->index(['job_post_id', 'event_type', 'view_date'], 'rjpv_post_event_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruitment_job_post_views');

        if (Schema::hasColumn('recruitment_job_posts', 'clicks')) {
            Schema::table('recruitment_job_posts', function (Blueprint $table) {
                $table->dropColumn('clicks');
            });
        }
    }
};
