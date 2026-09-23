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
        Schema::table('form_shares', function (Blueprint $table) {
            $table->unsignedBigInteger('revoked_by')->nullable()->after('expires_at')->comment('User ID who revoked the share');
            $table->timestamp('revoked_at')->nullable()->after('revoked_by')->comment('When the share was revoked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_shares', function (Blueprint $table) {
            $table->dropColumn(['revoked_by', 'revoked_at']);
        });
    }
};
