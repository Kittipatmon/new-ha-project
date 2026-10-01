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
        static::created(function ($model) {
            try {
                $module = property_exists($model, 'auditModule') ? $model->auditModule : null;
                $moduleName = property_exists($model, 'auditModuleName') ? $model->auditModuleName : null;
                $title = method_exists($model, 'getAuditTitle') ? $model->getAuditTitle() : ($model->title ?? $model->name ?? '#' . $model->getKey());

                AuditLogService::log(
                    action: 'created',
                    description: "สร้าง/เพิ่มข้อมูล: {$title}",
                    model: $model,
                    oldValues: null,
                    newValues: $model->getAttributes(),
                    module: $module,
                    moduleName: $moduleName
                );
            } catch (\Throwable $e) {
                logger()->error('Auditable created error: ' . $e->getMessage());
            }
        });

        static::updated(function ($model) {
            try {
                $module = property_exists($model, 'auditModule') ? $model->auditModule : null;
                $moduleName = property_exists($model, 'auditModuleName') ? $model->auditModuleName : null;
                $title = method_exists($model, 'getAuditTitle') ? $model->getAuditTitle() : ($model->title ?? $model->name ?? '#' . $model->getKey());

                $changes = $model->getChanges();

                // Fields to ignore from triggering "Update" audit logs (counters, timestamps, session tokens)
                $ignoredFields = [
                    'updated_at',
                    'created_at',
                    'views',
                    'clicks',
                    'impressions',
                    'last_login_at',
                    'last_seen_at',
                    'remember_token',
                ];

                // Remove ignored fields from changes
                $meaningfulChanges = array_diff_key($changes, array_flip($ignoredFields));

                // If no meaningful changes remain (e.g. only views counter incremented or updated_at touched), do not log
                if (empty($meaningfulChanges)) {
                    return;
                }

                $original = [];
                foreach (array_keys($meaningfulChanges) as $key) {
                    $original[$key] = $model->getOriginal($key);
                }

                AuditLogService::log(
                    action: 'updated',
                    description: "แก้ไขข้อมูล: {$title}",
                    model: $model,
                    oldValues: $original,
                    newValues: $meaningfulChanges,
                    module: $module,
                    moduleName: $moduleName
                );
            } catch (\Throwable $e) {
                logger()->error('Auditable updated error: ' . $e->getMessage());
            }
        });

        static::deleted(function ($model) {
            try {
                $module = property_exists($model, 'auditModule') ? $model->auditModule : null;
                $moduleName = property_exists($model, 'auditModuleName') ? $model->auditModuleName : null;
                $title = method_exists($model, 'getAuditTitle') ? $model->getAuditTitle() : ($model->title ?? $model->name ?? '#' . $model->getKey());

                AuditLogService::log(
                    action: 'deleted',
                    description: "ลบข้อมูล: {$title}",
                    model: $model,
                    oldValues: $model->getOriginal(),
                    newValues: null,
                    module: $module,
                    moduleName: $moduleName
                );
            } catch (\Throwable $e) {
                logger()->error('Auditable deleted error: ' . $e->getMessage());
            }
        });
    }
}
