<?php


namespace App\Models\hrrequest;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class RequestCategories extends Model
{
    use Auditable;

    public string $auditModule = 'hr_request';
    public string $auditModuleName = 'หมวดหมู่คำร้อง HR';

    public function getAuditTitle(): string
    {
        return "หมวดหมู่คำร้อง: " . ($this->name_th ?? $this->code ?? '#' . $this->getKey());
    }

    protected $table = 'request_categories';
    protected $primaryKey = 'id';

    protected $fillable = [
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
}
