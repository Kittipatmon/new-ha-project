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
        Schema::table('recruitment_applicants', function (Blueprint $table) {
            // Page 1: Personal info
            $table->string('nickname', 50)->nullable()->after('last_name');
            $table->string('house_no', 50)->nullable()->after('address');
            $table->string('moo', 50)->nullable()->after('house_no');
            $table->string('road', 100)->nullable()->after('moo');
            $table->string('subdistrict', 100)->nullable()->after('road');
            $table->string('district', 100)->nullable()->after('subdistrict');
            $table->string('postcode', 20)->nullable()->after('province');
            $table->string('tel', 50)->nullable()->after('phone');
            $table->string('facebook', 100)->nullable()->after('line_id');
            $table->string('housing_type', 50)->nullable()->after('facebook'); // อาศัยกับครอบครัว, บ้านตัวเอง, บ้านเช่า, หอพัก, คอนโดมิเนียม
            $table->integer('age')->nullable()->after('date_of_birth');
            $table->string('place_of_birth', 100)->nullable()->after('age');
            $table->string('race', 50)->nullable()->after('place_of_birth');
            $table->string('nationality', 50)->nullable()->after('race');
            $table->string('religion', 50)->nullable()->after('nationality');
            $table->string('id_card_issued_by', 100)->nullable()->after('national_id');
            $table->string('id_card_issued_province', 100)->nullable()->after('id_card_issued_by');
            $table->date('id_card_issued_date')->nullable()->after('id_card_issued_province');
            $table->date('id_card_expiry_date')->nullable()->after('id_card_issued_date');
            $table->decimal('height_cm', 5, 2)->nullable()->after('id_card_expiry_date');
            $table->decimal('weight_kg', 5, 2)->nullable()->after('height_cm');
            $table->string('military_status', 50)->nullable()->after('weight_kg'); // ได้รับการยกเว้น, ปลดเป็นทหารกองหนุน, ยังไม่ได้รับการเกณฑ์
            $table->string('marital_status', 50)->nullable()->after('military_status'); // โสด, แต่งงาน, หม้าย, แยกกัน

            // Page 2: Family Info & Emergency Contact
            $table->json('family_info')->nullable()->after('marital_status');
            $table->json('emergency_contact')->nullable()->after('family_info');

            // Page 3: Language Skills & Experience Details
            $table->json('language_skills')->nullable()->after('emergency_contact');
            $table->text('experience_summary')->nullable()->after('language_skills');

            // Page 4: Skills, Special Abilities & General Questions
            $table->text('computer_skills')->nullable()->after('experience_summary');
            $table->json('special_abilities')->nullable()->after('computer_skills');
            $table->json('application_questions')->nullable()->after('special_abilities');

            // Page 5: References & Questionnaire
            $table->json('references_info')->nullable()->after('application_questions');
            $table->json('health_criminal_questionnaire')->nullable()->after('references_info');
            $table->json('attached_documents_check')->nullable()->after('health_criminal_questionnaire');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitment_applicants', function (Blueprint $table) {
            $table->dropColumn([
                'nickname',
                'house_no',
                'moo',
                'road',
                'subdistrict',
                'district',
                'postcode',
                'tel',
                'facebook',
                'housing_type',
                'age',
                'place_of_birth',
                'race',
                'nationality',
                'religion',
                'id_card_issued_by',
                'id_card_issued_province',
                'id_card_issued_date',
                'id_card_expiry_date',
                'height_cm',
                'weight_kg',
                'military_status',
                'marital_status',
                'family_info',
                'emergency_contact',
                'language_skills',
                'experience_summary',
                'computer_skills',
                'special_abilities',
                'application_questions',
                'references_info',
                'health_criminal_questionnaire',
                'attached_documents_check',
            ]);
        });
    }
};
