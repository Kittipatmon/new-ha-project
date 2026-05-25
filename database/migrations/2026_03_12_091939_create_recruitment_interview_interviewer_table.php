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
        Schema::create('recruitment_interview_interviewer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained('recruitment_interviews')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->comment('ID ผู้สัมภาษณ์ จากตาราง userskml');
            $table->timestamps();

            // Index for faster lookups
            $table->index(['interview_id', 'user_id'], 'idx_interview_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruitment_interview_interviewer');
    }
};
