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
        Schema::table('manpower_requests', function (Blueprint $table) {
            $table->string('status')->default('pending_manager')->after('res_6'); // pending_manager, pending_vp, pending_hr, pending_ceo, approved, rejected
            
            $table->unsignedBigInteger('manager_approved_by')->nullable();
            $table->timestamp('manager_approved_at')->nullable();
            
            $table->unsignedBigInteger('vp_approved_by')->nullable();
            $table->timestamp('vp_approved_at')->nullable();
            
            $table->unsignedBigInteger('hr_approved_by')->nullable();
            $table->timestamp('hr_approved_at')->nullable();
            
            $table->unsignedBigInteger('ceo_approved_by')->nullable();
            $table->timestamp('ceo_approved_at')->nullable();
            
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manpower_requests', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'manager_approved_by', 'manager_approved_at',
                'vp_approved_by', 'vp_approved_at',
                'hr_approved_by', 'hr_approved_at',
                'ceo_approved_by', 'ceo_approved_at',
                'rejected_by', 'rejected_at', 'rejection_reason'
            ]);
        });
    }
};
