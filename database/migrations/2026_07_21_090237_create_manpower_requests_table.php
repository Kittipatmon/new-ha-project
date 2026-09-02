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
        Schema::create('manpower_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('date');
            $table->string('department');
            $table->string('section');
            $table->string('job_title_th');
            $table->string('job_title_en')->nullable();
            $table->integer('headcount');
            $table->integer('current_headcount')->nullable();
            $table->date('expected_start_date')->nullable();
            
            $table->string('job_level')->nullable();
            
            $table->string('hire_type')->nullable();
            $table->string('hire_replacement_name')->nullable();
            $table->string('hire_transfer_name')->nullable();
            $table->date('hire_temp_start')->nullable();
            $table->date('hire_temp_end')->nullable();
            
            $table->boolean('attachment_org_chart')->default(false);
            $table->boolean('attachment_jd')->default(false);
            $table->boolean('attachment_manpower_plan')->default(false);
            
            $table->string('req_gender')->nullable();
            $table->string('req_age')->nullable();
            $table->string('req_education')->nullable();
            $table->string('req_major')->nullable();
            $table->string('req_experience')->nullable();
            $table->string('req_special')->nullable();
            $table->string('req_other')->nullable();
            
            $table->string('res_1')->nullable();
            $table->string('res_2')->nullable();
            $table->string('res_3')->nullable();
            $table->string('res_4')->nullable();
            $table->string('res_5')->nullable();
            $table->string('res_6')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manpower_requests');
    }
};
