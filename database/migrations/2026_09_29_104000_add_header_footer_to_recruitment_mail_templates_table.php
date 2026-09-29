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
            $table->string('header_logo_url')->nullable()->after('theme_color');
            $table->string('header_tagline')->nullable()->after('header_logo_url');
            $table->string('footer_salutation')->nullable()->after('closing_text');
            $table->string('sender_name')->nullable()->after('footer_salutation');
            $table->string('sender_position')->nullable()->after('sender_name');
            $table->string('company_name')->nullable()->after('sender_position');
            $table->string('contact_phone')->nullable()->after('company_name');
            $table->string('contact_website')->nullable()->after('contact_phone');
            $table->string('contact_email')->nullable()->after('contact_website');
            $table->text('footer_copyright')->nullable()->after('contact_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitment_mail_templates', function (Blueprint $table) {
            $table->dropColumn([
                'header_logo_url',
                'header_tagline',
                'footer_salutation',
                'sender_name',
                'sender_position',
                'company_name',
                'contact_phone',
                'contact_website',
                'contact_email',
                'footer_copyright',
            ]);
        });
    }
};
