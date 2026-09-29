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
        Schema::create('recruitment_mail_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('category')->default('candidate'); // candidate, internal
            $table->string('theme_color')->default('#ea580c');
            $table->string('subject');
            $table->string('title');
            $table->string('badge_text')->nullable();
            $table->string('greeting')->nullable();
            $table->text('body_text')->nullable();
            $table->string('notice_title')->nullable();
            $table->text('notice_text')->nullable();
            $table->text('closing_text')->nullable();
            $table->json('extra_data')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruitment_mail_templates');
    }
};
