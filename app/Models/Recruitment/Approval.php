<?php

namespace App\Models\Recruitment;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approval extends Model
{
    protected $table = 'recruitment_approvals';

    protected $fillable = [
        'module_type',
        'module_id',
        'step_name',
        'approver_id',
        'status',
        'approved_at',
        'comment',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function module()
    {
        return $this->morphTo(null, 'module_type', 'module_id');
    }
}
