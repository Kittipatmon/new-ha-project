<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $connection = 'userkml2025';
        $table = 'userskml';

        if (!Schema::connection($connection)->hasTable($table)) {
            return;
        }

        $hasEndworkComment = Schema::connection($connection)->hasColumn($table, 'endwork_comment');
        $hasEndworkDate = Schema::connection($connection)->hasColumn($table, 'endwork_date');

        if ($hasEndworkComment && $hasEndworkDate) {
            return;
        }

        Schema::connection($connection)->table($table, function (Blueprint $table) use ($hasEndworkComment, $hasEndworkDate) {
            if (!$hasEndworkDate) {
                $table->date('endwork_date')->nullable();
            }
            if (!$hasEndworkComment) {
                $table->text('endwork_comment')->nullable();
            }
        });
    }

    public function down(): void
    {
        $connection = 'userkml2025';
        $table = 'userskml';

        if (!Schema::connection($connection)->hasTable($table)) {
            return;
        }

        $hasEndworkComment = Schema::connection($connection)->hasColumn($table, 'endwork_comment');
        $hasEndworkDate = Schema::connection($connection)->hasColumn($table, 'endwork_date');

        if (!$hasEndworkComment && !$hasEndworkDate) {
            return;
        }

        Schema::connection($connection)->table($table, function (Blueprint $table) use ($hasEndworkComment, $hasEndworkDate) {
            if ($hasEndworkComment) {
                $table->dropColumn('endwork_comment');
            }
            if ($hasEndworkDate) {
                $table->dropColumn('endwork_date');
            }
        });
    }
};
