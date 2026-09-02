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
        Schema::create('probation_exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('probation_evaluation_id')->constrained()->cascadeOnDelete();
            $table->string('topic');
            $table->integer('passed_round')->nullable(); // 1, 2, or 3
            $table->date('exam_date')->nullable();
            $table->string('exam_tester')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('probation_exam_results');
    }
};
