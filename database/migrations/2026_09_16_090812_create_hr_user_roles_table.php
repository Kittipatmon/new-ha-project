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
        // สร้างตาราง hr_user_roles บน connection mysql (Database: hrsystem)
        if (!Schema::connection('mysql')->hasTable('hr_user_roles')) {
            Schema::connection('mysql')->create('hr_user_roles', function (Blueprint $table) {
                $table->id();
                $table->string('employee_code', 50)->unique()->comment('รหัสพนักงาน emp_code เชื่อมกับ appkum_user.employees');
                $table->unsignedBigInteger('employee_id')->nullable()->index()->comment('ID ของ employee ใน appkum_user');
                $table->enum('role', ['admin', 'editor', 'viewer'])->default('viewer')->comment('สิทธิ์ของระบบ HR (admin/editor/viewer)');
                $table->text('remark')->nullable()->comment('หมายเหตุ');
                $table->timestamps();
            });
        }

        // คัดลอกพนักงานที่มี role = 'admin' จาก appkum_user.employees มาใส่เป็น admin ใน hr_user_roles เบื้องต้น
        try {
            $adminEmps = DB::connection('userkml2025')->table('employees')->where('role', 'admin')->get();
            foreach ($adminEmps as $emp) {
                if (!empty($emp->emp_code)) {
                    DB::connection('mysql')->table('hr_user_roles')->updateOrInsert(
                        ['employee_code' => (string)$emp->emp_code],
                        [
                            'employee_id' => $emp->id,
                            'role' => 'admin',
                            'remark' => 'Migrated from central admin',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        } catch (\Throwable $e) {
            // Ignore if connection or records not available
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists('hr_user_roles');
    }
};
