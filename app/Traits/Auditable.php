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
                    description: "เพิ่มข้อมูล {$title}",
                    model: $model,
                    oldValues: null,
                    newValues: $model->getAttributes(),
                    module: $module,
                    moduleName: $moduleName
                );
            } catch (\Throwable $e) {
                // Fail silently to prevent audit logging from breaking transactions
                logger()->error('Auditable created error: ' . $e->getMessage());
            }
        });

        static::updated(function ($model) {
            try {
                $module = property_exists($model, 'auditModule') ? $model->auditModule : null;
                $moduleName = property_exists($model, 'auditModuleName') ? $model->auditModuleName : null;
                $title = method_exists($model, 'getAuditTitle') ? $model->getAuditTitle() : ($model->title ?? $model->name ?? '#' . $model->getKey());

                $changes = $model->getChanges();
                // If only updated_at changed, ignore
                if (count($changes) === 1 && isset($changes['updated_at'])) {
                    return;
                }

                $original = [];
                foreach (array_keys($changes) as $key) {
                    $original[$key] = $model->getOriginal($key);
                }

                AuditLogService::log(
                    action: 'updated',
                    description: "แก้ไขข้อมูล {$title}",
                    model: $model,
                    oldValues: $original,
                    newValues: $changes,
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
                    description: "ลบข้อมูล {$title}",
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
