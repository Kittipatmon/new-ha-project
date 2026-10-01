<?php

namespace App\Models\hrrequest;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class RequestSubtypes extends Model
{
    use Auditable;

    public string $auditModule = 'hr_request';
    public string $auditModuleName = 'ประเภทย่อยคำร้อง HR';

    public function getAuditTitle(): string
    {
        return "ประเภทย่อยคำร้อง: " . ($this->name_th ?? $this->code ?? '#' . $this->getKey());
    }

    protected $table = 'request_subtype';
    protected $primaryKey = 'id';

    protected $fillable = [
        'type_id',
        'code',
        'name_th',
        'name_en',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $timestamps = true;

    public function requestType()
    {
        return $this->belongsTo(RequestType::class, 'type_id');
    }
}
