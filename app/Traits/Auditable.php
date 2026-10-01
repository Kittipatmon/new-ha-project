<?php

namespace App\Traits;

use App\Services\AuditLogService;

trait Auditable
{
    /**
     * Boot the trait and register model event observers
     */
    public static function bootAuditable(): void
    {
        // Routine website CRUD actions are not logged to prevent audit log flooding.
        // Audit logs are reserved for critical administrative actions (Microsoft 365 settings, DB backups, etc.)
    }
}
