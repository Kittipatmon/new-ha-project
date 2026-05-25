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
        Schema::table('recruitment_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('approver_manager_id')->nullable()->after('requested_by');
            $table->unsignedBigInteger('approver_executive_id')->nullable()->after('approver_manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitment_requests', function (Blueprint $table) {
            $table->dropColumn(['approver_manager_id', 'approver_executive_id']);
        });
    }
};
