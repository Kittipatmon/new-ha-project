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
            $table->string('requester_name')->nullable();
            $table->date('requester_date')->nullable();
            $table->string('manager_name')->nullable();
            $table->date('manager_date')->nullable();
            $table->string('vp_name')->nullable();
            $table->date('vp_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manpower_requests', function (Blueprint $table) {
            $table->dropColumn([
                'requester_name', 'requester_date', 
                'manager_name', 'manager_date', 
                'vp_name', 'vp_date'
            ]);
        });
    }
};
