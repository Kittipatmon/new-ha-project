<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class FormShare extends Model
{
    use Auditable;

    public string $auditModule = 'form_share';
    public string $auditModuleName = 'การแชร์และกำหนดสิทธิ์เอกสาร';

    public function getAuditTitle(): string
    {
        return "แชร์เอกสาร: {$this->form_type} #{$this->form_id}";
    }

    protected $table = 'form_shares';

    protected $fillable = [
        'form_type',
        'form_id',
        'shared_by',
        'shared_to_user_id',
        'shared_to_dept_id',
        'share_channel',
        'note',
        'access_token',
        'expires_at',
        'revoked_by',
        'revoked_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'shared_by');
    }

    public function recipientUser()
    {
        return $this->belongsTo(User::class, 'shared_to_user_id');
    }

    public function recipientDept()
    {
        return $this->belongsTo(Department::class, 'shared_to_dept_id', 'department_id');
    }

    public function revokedByUser()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    /**
     * Check if this share has been revoked.
     */
    public function isRevoked(): bool
    {
        return !is_null($this->revoked_at);
    }

    /**
     * Scope to only active (non-revoked) shares.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('revoked_at');
    }

    /**
     * Check if a specific user has access to a given form through shares
     */
    public static function hasAccess($formType, $formId, $user = null): bool
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return false;
        }

        // Check if shared to all users (shared_to_user_id = 0), specifically to this user, or to their department
        return self::where('form_type', $formType)
            ->where('form_id', $formId)
            ->whereNull('revoked_at')
            ->where(function ($q) use ($user) {
                $q->where('shared_to_user_id', 0)
                  ->orWhere('shared_to_user_id', $user->id);
                if (!empty($user->dept_id)) {
                    $q->orWhere('shared_to_dept_id', $user->dept_id);
                }
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    /**
     * Get list of form IDs shared with a specific user or their department
     */
    public static function getAccessibleFormIds(string $formType, $user = null): array
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return [];
        }

        return self::where('form_type', $formType)
            ->whereNull('revoked_at')
            ->where(function ($q) use ($user) {
                $q->where('shared_to_user_id', 0)
                  ->orWhere('shared_to_user_id', $user->id);
                if (!empty($user->dept_id)) {
                    $q->orWhere('shared_to_dept_id', $user->dept_id);
                }
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->pluck('form_id')
            ->toArray();
    }
}

