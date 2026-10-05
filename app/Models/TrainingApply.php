<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingApply extends Model
{
    use HasFactory, Auditable;

    public string $auditModule = 'training';
    public string $auditModuleName = 'ระบบฝึกอบรม';

    public function getAuditTitle(): string
    {
        return "สมัครฝึกอบรม: รหัสพนักงาน {$this->employee_code} (หลักสูตร #{$this->training_id})";
    }

    protected $connection = 'mysql';

    protected $fillable = [
        'training_id',
        'employee_code',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'employee_code', 'emp_code');
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}
