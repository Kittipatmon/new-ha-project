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
        if (!Schema::hasTable('suggestions')) {
            Schema::create('suggestions', function (Blueprint $table) {
                $table->id();
                $table->string('complaint_type'); // self, other, phone
                $table->string('topic');
                $table->string('to_person');
                $table->unsignedBigInteger('user_id')->nullable(); // For authenticated user, if applicable
                $table->string('fullname');
                $table->integer('age')->nullable();
                $table->string('phone');
                $table->string('address_no')->nullable();
                $table->string('moo')->nullable();
                $table->string('soi')->nullable();
                $table->string('road')->nullable();
                $table->string('subdistrict')->nullable();
                $table->string('district')->nullable();
                $table->string('province')->nullable();
                $table->text('details');
                $table->text('demands');
                $table->json('docs')->nullable(); // JSON array for id_card, other
                $table->string('other_docs_detail')->nullable();
                $table->json('attachments')->nullable(); // JSON array of file paths
                $table->string('history'); // never, ever
                $table->string('status')->default('รับเรื่อง');
                $table->text('progress_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggestions');
    }
};
