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
        Schema::table('recruitment_mail_templates', function (Blueprint $table) {
            $table->dropForeign('recruitment_mail_templates_updated_by_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitment_mail_templates', function (Blueprint $table) {
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }
};
