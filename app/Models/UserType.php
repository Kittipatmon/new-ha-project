<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use Auditable;

    public string $auditModule = 'master_data';
    public string $auditModuleName = 'ประเภทผู้ใช้งาน';

    public function getAuditTitle(): string
    {
        return "ประเภทผู้ใช้งาน: " . ($this->type_name ?? '#' . $this->getKey());
    }

    protected $connection = 'mysql';

    protected $table = 'user_types';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'type_code',
        'type_name',
        'status',
        'description',
    ];

    protected $casts = [
        'status' => 'integer',
    ];
}
