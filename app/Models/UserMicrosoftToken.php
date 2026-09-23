<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMicrosoftToken extends Model
{
    protected $connection = 'mysql';
    protected $table = 'user_microsoft_tokens';

    protected $fillable = [
        'user_id',
        'microsoft_email',
        'microsoft_name',
        'access_token',
        'refresh_token',
        'expires_at',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if token is expired or expires within 5 minutes.
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return true;
        }

        return $this->expires_at->subMinutes(5)->isPast();
    }
}
