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
        Schema::table('database_backups', function (Blueprint $table) {
            $table->boolean('is_encrypted')->default(true)->after('checksum_sha256');
            $table->string('encryption_algorithm', 50)->default('AES-256')->after('is_encrypted');
            $table->text('encrypted_password')->nullable()->after('encryption_algorithm');
            $table->string('md_file_path')->nullable()->after('encrypted_password');
            $table->string('email_sent_to')->nullable()->after('md_file_path');
            $table->dateTime('email_sent_at')->nullable()->after('email_sent_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('database_backups', function (Blueprint $table) {
            $table->dropColumn([
                'is_encrypted',
                'encryption_algorithm',
                'encrypted_password',
                'md_file_path',
                'email_sent_to',
                'email_sent_at',
            ]);
        });
    }
};
