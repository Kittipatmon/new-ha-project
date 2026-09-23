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
        // Add views and clicks counters to posters table if not exists
        if (!Schema::hasColumn('posters', 'views')) {
            Schema::table('posters', function (Blueprint $table) {
                $table->unsignedBigInteger('views')->default(0)->after('sort_order');
                $table->unsignedBigInteger('clicks')->default(0)->after('views');
            });
        }

        // Detailed view & click tracking table
        Schema::create('poster_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poster_id')->constrained('posters')->onDelete('cascade');
            $table->string('event_type')->default('view'); // 'view' or 'click'
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->date('view_date');
            $table->timestamps();

            $table->index(['poster_id', 'event_type', 'view_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poster_views');
    }
};
