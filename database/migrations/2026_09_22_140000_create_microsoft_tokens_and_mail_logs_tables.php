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
        if (!Schema::hasTable('user_microsoft_tokens')) {
            Schema::create('user_microsoft_tokens', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('microsoft_email')->nullable();
                $table->string('microsoft_name')->nullable();
                $table->longText('access_token');
                $table->longText('refresh_token')->nullable();
                $table->dateTime('expires_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('recruitment_mail_logs')) {
            Schema::create('recruitment_mail_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('recipient_email')->index();
                $table->string('recipient_name')->nullable();
                $table->string('subject');
                $table->string('mail_type', 50)->default('direct_email')->index(); // interview_scheduled, application_hired, application_rejected, direct_email
                $table->string('channel', 30)->default('microsoft_graph'); // microsoft_graph, smtp
                $table->string('status', 30)->default('queued')->index(); // queued, sent, failed
                $table->text('error_message')->nullable();
                $table->json('payload')->nullable();
                $table->dateTime('sent_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruitment_mail_logs');
        Schema::dropIfExists('user_microsoft_tokens');
    }
};
