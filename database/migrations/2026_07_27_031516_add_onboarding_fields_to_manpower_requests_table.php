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
            $table->string('onboard_employee_code')->nullable()->after('hr_approved_at');
            $table->string('onboard_employee_name')->nullable()->after('onboard_employee_code');
            $table->date('onboard_date')->nullable()->after('onboard_employee_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manpower_requests', function (Blueprint $table) {
            $table->dropColumn(['onboard_employee_code', 'onboard_employee_name', 'onboard_date']);
        });
    }
};
