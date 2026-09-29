<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use Auditable;

    public string $auditModule = 'training';
    public string $auditModuleName = 'ระบบฝึกอบรม';

    public function getAuditTitle(): string
    {
        return "หลักสูตรฝึกอบรม: {$this->details}";
    }

    protected $connection = 'mysql';

    protected $fillable = [
        'branch',
        'hours',
        'format',
        'start_date',
        'end_date',
        'department',
        'status',
        'details',
        'document',
        'document_link',
        'image',
    ];

    public function applies()
    {
        return $this->hasMany(TrainingApply::class);
    }
}
