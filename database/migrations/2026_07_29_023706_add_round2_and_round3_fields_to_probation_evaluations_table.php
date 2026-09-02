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
        Schema::table('probation_evaluations', function (Blueprint $table) {
            // Round 2
            $table->text('tasks_assigned_2')->nullable();
            $table->text('performance_result_2')->nullable();
            $table->string('performance_level_2')->nullable();
            $table->string('evaluation_result_2')->nullable();
            $table->string('evaluation_result_reason_2')->nullable();
            $table->text('evaluator_comment_2')->nullable();
            $table->text('hr_comment_2')->nullable();

            // Round 3
            $table->text('tasks_assigned_3')->nullable();
            $table->text('performance_result_3')->nullable();
            $table->string('performance_level_3')->nullable();
            $table->string('evaluation_result_3')->nullable();
            $table->string('evaluation_result_reason_3')->nullable();
            $table->text('evaluator_comment_3')->nullable();
            $table->text('hr_comment_3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('probation_evaluations', function (Blueprint $table) {
            $table->dropColumn([
                'tasks_assigned_2', 'performance_result_2', 'performance_level_2', 
                'evaluation_result_2', 'evaluation_result_reason_2', 'evaluator_comment_2', 'hr_comment_2',
                'tasks_assigned_3', 'performance_result_3', 'performance_level_3', 
                'evaluation_result_3', 'evaluation_result_reason_3', 'evaluator_comment_3', 'hr_comment_3'
            ]);
        });
    }
};
