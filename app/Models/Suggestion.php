<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    use Auditable;

    public string $auditModule = 'suggestion';
    public string $auditModuleName = 'ระบบรับเรื่องร้องเรียนและข้อเสนอแนะ';

    public function getAuditTitle(): string
    {
        $name = $this->fullname ? " ({$this->fullname})" : '';
        return "ข้อเสนอแนะ: " . ($this->topic ?? '#' . $this->getKey()) . $name;
    }

    protected $fillable = [
        'complaint_type',
        'topic',
        'to_person',
        'user_id',
        'fullname',
        'age',
        'phone',
        'address_no',
        'moo',
        'soi',
        'road',
        'subdistrict',
        'district',
        'province',
        'details',
        'demands',
        'docs',
        'other_docs_detail',
        'attachments',
        'history',
        'status',
        'progress_notes',
    ];

    protected $casts = [
        'docs' => 'array',
        'attachments' => 'array',
    ];
}
