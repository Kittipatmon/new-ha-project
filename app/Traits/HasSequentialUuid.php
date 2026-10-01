<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSequentialUuid
{
    /**
     * Boot trait to automatically assign a sequential UUID on creation
     */
    public static function bootHasSequentialUuid(): void
    {
        static::creating(function (Model $model) {
            $uuidCol = method_exists($model, 'getUuidColumn') ? $model->getUuidColumn() : 'uuid';

            if (empty($model->{$uuidCol})) {
                $model->{$uuidCol} = (string) Str::orderedUuid();
            }
        });
    }

    /**
     * Get the column name for the UUID
     */
    public function getUuidColumn(): string
    {
        return property_exists($this, 'uuidColumn') ? $this->uuidColumn : 'uuid';
    }

    /**
     * Dual-resolution Route Model Binding:
     * Supports both UUID string and legacy numeric ID seamlessly
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Eloquent\Builder
     */
    public function resolveRouteBindingQuery($query, $value, $field = null)
    {
        if ($field) {
            return parent::resolveRouteBindingQuery($query, $value, $field);
        }

        $uuidCol = $this->getUuidColumn();

        // Check if value is a valid UUID format (36 chars with hyphens)
        if (is_string($value) && preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value)) {
            return $query->where($uuidCol, $value);
        }

        // Fallback to primary key (ID) for full backwards compatibility
        return $query->where($this->getQualifiedKeyName(), $value);
    }
}
