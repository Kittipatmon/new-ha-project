<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class HrUserRole extends Model
{
    use Auditable;

    public string $auditModule = 'users';
    public string $auditModuleName = 'กำหนดสิทธิ์ระบบ HR';

    public function getAuditTitle(): string
    {
        return "สิทธิ์ HR: รหัสพนักงาน {$this->employee_code} [บทบาท: " . strtoupper($this->role ?? '') . "]";
    }

    protected $connection = 'mysql';

    protected $table = 'hr_user_roles';

    protected $fillable = [
        'employee_code',
        'employee_id',
        'role',
        'remark',
    ];

    const ROLE_ADMIN = 'admin';
    const ROLE_EDITOR = 'editor';
    const ROLE_VIEWER = 'viewer';

    public static function getAvailableRoles(): array
    {
        return [
            self::ROLE_ADMIN => [
                'label' => 'ADMIN',
                'description' => 'ผู้ดูแลระบบ (เข้าถึงระบบ HR ได้ทั้งหมด)',
                'badge_class' => 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-300 dark:border-purple-800',
            ],
            self::ROLE_EDITOR => [
                'label' => 'EDITOR',
                'description' => 'ผู้แก้ไข (ดูข้อมูล เพิ่ม แก้ไข - ห้ามลบ/ห้ามจัดการสิทธิ์)',
                'badge_class' => 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-300 dark:border-blue-800',
            ],
            self::ROLE_VIEWER => [
                'label' => 'VIEWER',
                'description' => 'ผู้ดูข้อมูล (ดูข้อมูลได้อย่างเดียว)',
                'badge_class' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-700',
            ],
        ];
    }

    /**
     * Relationship to central employee User
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_code', 'emp_code');
    }
}
