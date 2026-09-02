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
        Schema::create('probation_absence_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('probation_evaluation_id')->constrained()->cascadeOnDelete();
            $table->integer('round');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('business_leave')->nullable();
            $table->integer('sick_leave')->nullable();
            $table->integer('absent')->nullable();
            $table->integer('late_count')->nullable();
            $table->integer('late_mins')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('probation_absence_records');
    }
};
