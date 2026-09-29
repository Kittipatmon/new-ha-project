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
        if (!Schema::hasTable('system_audit_logs')) {
            Schema::create('system_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('user_code')->nullable();
                $table->string('user_name')->nullable();
                $table->string('user_email')->nullable();
                $table->string('user_role')->nullable();
                $table->string('action', 50)->index(); // created, updated, deleted, login, logout, archived, etc.
                $table->string('module', 50)->index(); // recruitment, training, users, settings, system
                $table->string('module_name', 100)->nullable();
                $table->string('model_type')->nullable()->index();
                $table->string('model_id')->nullable()->index();
                $table->text('description');
                $table->longText('old_values')->nullable();
                $table->longText('new_values')->nullable();
                $table->longText('diff')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->text('url')->nullable();
                $table->string('method', 10)->nullable();
                $table->timestamps();

                $table->index(['created_at', 'action']);
                $table->index(['created_at', 'module']);
            });
        }

        if (!Schema::hasTable('system_audit_archives')) {
            Schema::create('system_audit_archives', function (Blueprint $table) {
                $table->id();
                $table->string('filename');
                $table->string('file_path');
                $table->unsignedBigInteger('file_size')->default(0);
                $table->string('file_size_human', 50)->default('0 KB');
                $table->unsignedInteger('records_count')->default(0);
                $table->dateTime('period_start');
                $table->dateTime('period_end');
                $table->string('period_label', 100);
                $table->string('checksum_sha256', 64);
                $table->unsignedBigInteger('archived_by')->nullable();
                $table->string('archived_by_name')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_audit_archives');
        Schema::dropIfExists('system_audit_logs');
    }
};
