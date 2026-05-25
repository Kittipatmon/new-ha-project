<?php

namespace App\Models\Recruitment;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusLog extends Model
{
    protected $table = 'recruitment_status_logs';

    protected $fillable = [
        'application_id',
        'old_status',
        'new_status',
        'changed_by',
        'changed_at',
        'remark',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
