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
        Schema::create('form_shares', function (Blueprint $table) {
            $table->id();
            $table->string('form_type', 50); // manpower_request, probation_evaluation, interview_evaluation, hr_request
            $table->unsignedBigInteger('form_id');
            $table->unsignedBigInteger('shared_by')->comment('User ID who shared');
            $table->unsignedBigInteger('shared_to_user_id')->nullable()->comment('Target User ID');
            $table->unsignedBigInteger('shared_to_dept_id')->nullable()->comment('Target Department ID');
            $table->string('share_channel', 50)->default('direct_link')->comment('link, email, chat, department, etc.');
            $table->text('note')->nullable()->comment('Sharing note/remarks');
            $table->string('access_token', 64)->nullable()->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['form_type', 'form_id']);
            $table->index('shared_to_user_id');
            $table->index('shared_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_shares');
    }
};
