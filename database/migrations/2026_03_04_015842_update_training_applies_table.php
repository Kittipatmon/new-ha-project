<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('training_applies', function (Blueprint $table) {
            if (!Schema::hasColumn('training_applies', 'employee_code')) {
                $table->string('employee_code')->after('training_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_applies', function (Blueprint $table) {
            $table->dropColumn('employee_code');
        });
    }
};
