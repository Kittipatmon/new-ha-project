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
        Schema::create('probation_evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('prefix')->nullable();
            $table->string('employee_name');
            $table->string('position')->nullable();
            $table->string('emp_code')->nullable();
            $table->string('department')->nullable();
            $table->date('start_date')->nullable();
            $table->date('probation_due_date')->nullable();
            
            // 1.3 Performance Evaluation
            $table->text('tasks_assigned')->nullable();
            $table->text('performance_result')->nullable();
            $table->string('performance_level')->nullable();
            $table->string('evaluation_result')->nullable();
            $table->string('evaluation_result_reason')->nullable();
            
            $table->text('evaluator_comment')->nullable();
            $table->text('hr_comment')->nullable();
            
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('probation_evaluations');
    }
};
