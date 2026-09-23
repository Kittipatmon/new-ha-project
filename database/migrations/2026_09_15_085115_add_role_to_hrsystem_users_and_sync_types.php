<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ensure role column exists in hrsystem.users
        if (Schema::connection('mysql')->hasTable('users')) {
            Schema::connection('mysql')->table('users', function (Blueprint $table) {
                if (!Schema::connection('mysql')->hasColumn('users', 'role')) {
                    $table->string('role', 50)->default('viewer')->after('hr_level');
                }
            });
        }

        // 2. Ensure role column exists in hrsystem.usersnew if present
        if (Schema::connection('mysql')->hasTable('usersnew')) {
            Schema::connection('mysql')->table('usersnew', function (Blueprint $table) {
                if (!Schema::connection('mysql')->hasColumn('usersnew', 'role')) {
                    $table->string('role', 50)->default('viewer')->after('level_user');
                }
            });
        }

        // 3. Keep employees table role as ENUM('admin', 'staff')
        if (Schema::connection('userkml2025')->hasTable('employees')) {
            try {
                DB::connection('userkml2025')->statement("ALTER TABLE `employees` MODIFY `role` ENUM('admin', 'staff') NOT NULL DEFAULT 'staff'");
            } catch (\Throwable $e) {
                // Ignore if not permitted
            }
        }

        // 4. Ensure ADMIN, EDITOR, VIEWER exist in hrsystem.user_types
        if (Schema::connection('mysql')->hasTable('user_types')) {
            // ADMIN
            $admin = DB::connection('mysql')->table('user_types')->where('type_code', 'ADMIN')->first();
            if (!$admin) {
                DB::connection('mysql')->table('user_types')->insert([
                    'id' => 0,
                    'type_code' => 'ADMIN',
                    'type_name' => 'ผู้ดูแลระบบ (ADMIN)',
                    'status' => 1,
                    'description' => 'เข้าถึงระบบได้ทั้งหมด',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::connection('mysql')->table('user_types')->where('type_code', 'ADMIN')->update([
                    'description' => 'เข้าถึงระบบได้ทั้งหมด',
                ]);
            }

            // EDITOR
            $editor = DB::connection('mysql')->table('user_types')->where('type_code', 'EDITOR')->first();
            if (!$editor) {
                $maxId = DB::connection('mysql')->table('user_types')->max('id');
                DB::connection('mysql')->table('user_types')->insert([
                    'id' => ($maxId !== null && $maxId < 10) ? 10 : ($maxId + 1),
                    'type_code' => 'EDITOR',
                    'type_name' => 'ผู้แก้ไข (EDITOR)',
                    'status' => 1,
                    'description' => 'ดูข้อมูล เพิ่มข้อมูล แก้ไขข้อมูล (ห้ามลบ/ห้ามจัดการ User)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // VIEWER
            $viewer = DB::connection('mysql')->table('user_types')->where('type_code', 'VIEWER')->first();
            if (!$viewer) {
                $maxId = DB::connection('mysql')->table('user_types')->max('id');
                DB::connection('mysql')->table('user_types')->insert([
                    'id' => ($maxId !== null && $maxId < 11) ? 11 : ($maxId + 1),
                    'type_code' => 'VIEWER',
                    'type_name' => 'ผู้ดูข้อมูล (VIEWER)',
                    'status' => 1,
                    'description' => 'ดูข้อมูลอย่างเดียว',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::connection('mysql')->hasTable('users')) {
            Schema::connection('mysql')->table('users', function (Blueprint $table) {
                if (Schema::connection('mysql')->hasColumn('users', 'role')) {
                    $table->dropColumn('role');
                }
            });
        }

        if (Schema::connection('mysql')->hasTable('usersnew')) {
            Schema::connection('mysql')->table('usersnew', function (Blueprint $table) {
                if (Schema::connection('mysql')->hasColumn('usersnew', 'role')) {
                    $table->dropColumn('role');
                }
            });
        }
    }
};
