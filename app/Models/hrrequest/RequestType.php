<?php

namespace App\Models\hrrequest;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class RequestType extends Model
{
    use Auditable;

    public string $auditModule = 'hr_request';
    public string $auditModuleName = 'ประเภทคำร้อง HR';

    public function getAuditTitle(): string
    {
        return "ประเภทคำร้อง: " . ($this->name_th ?? $this->code ?? '#' . $this->getKey());
    }

    protected $table = 'request_type';
    protected $primaryKey = 'id';

    protected $fillable = [
        'category_id',
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

    public function requestCategory()
    {
        return $this->belongsTo(RequestCategories::class, 'category_id');
    }
}
