<?php

namespace App\Models\Recruitment\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

class CrossConnectionBelongsToMany extends Relation
{
    protected string $pivotTable;
    protected string $foreignPivotKey;
    protected string $relatedPivotKey;
    protected string $pivotConnection;
    protected $eagerPivots = null;

    public function __construct(
        Builder $query,
        Model $parent,
        string $pivotTable,
        string $foreignPivotKey,
        string $relatedPivotKey,
        ?string $pivotConnection = null
    ) {
        $this->pivotTable = $pivotTable;
        $this->foreignPivotKey = $foreignPivotKey;
        $this->relatedPivotKey = $relatedPivotKey;
        $this->pivotConnection = $pivotConnection ?: $parent->getConnectionName() ?: 'mysql';

        parent::__construct($query, $parent);
    }

    /**
     * Set the base constraints for a lazy-load.
     */
    public function addConstraints()
    {
        if (static::$constraints && $this->parent->exists) {
            $userIds = DB::connection($this->pivotConnection)
                ->table($this->pivotTable)
                ->where($this->foreignPivotKey, $this->parent->getKey())
                ->pluck($this->relatedPivotKey)
                ->toArray();

            $this->query->whereIn($this->related->getQualifiedKeyName(), $userIds ?: [0]);
        }
    }

    /**
     * Set the constraints for an eager load of the relation.
     */
    public function addEagerConstraints(array $models)
    {
        $parentKeys = collect($models)->pluck($this->parent->getKeyName())->filter()->unique()->values()->toArray();

        if (empty($parentKeys)) {
            $this->eagerPivots = collect();
            $this->query->whereRaw('1 = 0');
            return;
        }

        $pivots = DB::connection($this->pivotConnection)
            ->table($this->pivotTable)
            ->whereIn($this->foreignPivotKey, $parentKeys)
            ->get();

        $this->eagerPivots = $pivots;

        $relatedKeys = $pivots->pluck($this->relatedPivotKey)->filter()->unique()->values()->toArray();

        $this->query->whereIn($this->related->getQualifiedKeyName(), $relatedKeys ?: [0]);
    }

    /**
     * Initialize the relation on a set of models.
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    /**
     * Match the eagerly loaded results to their parents.
     */
    public function match(array $models, Collection $results, $relation)
    {
        $dictionary = [];
        foreach ($this->eagerPivots ?? [] as $pivot) {
            $dictionary[$pivot->{$this->foreignPivotKey}][] = $pivot->{$this->relatedPivotKey};
        }

        $resultsKeyed = $results->keyBy($this->related->getKeyName());

        foreach ($models as $model) {
            $parentKey = $model->getKey();
            $assigned = $this->related->newCollection();

            if (isset($dictionary[$parentKey])) {
                foreach ($dictionary[$parentKey] as $relatedKey) {
                    if (isset($resultsKeyed[$relatedKey])) {
                        $assigned->push($resultsKeyed[$relatedKey]);
                    }
                }
            }

            $model->setRelation($relation, $assigned);
        }

        return $models;
    }

    /**
     * Get the results of the relationship for lazy loading.
     */
    public function getResults()
    {
        if (is_null($this->parent->getKey())) {
            return $this->related->newCollection();
        }

        $userIds = DB::connection($this->pivotConnection)
            ->table($this->pivotTable)
            ->where($this->foreignPivotKey, $this->parent->getKey())
            ->pluck($this->relatedPivotKey)
            ->toArray();

        if (empty($userIds)) {
            return $this->related->newCollection();
        }

        return $this->query->whereIn($this->related->getQualifiedKeyName(), $userIds)->get();
    }

    /**
     * Add the constraints for a relationship query (whereHas, has).
     */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        return $query->whereExists(function ($subQuery) use ($parentQuery) {
            $subQuery->from($this->pivotTable)
                ->whereColumn(
                    "{$this->pivotTable}.{$this->foreignPivotKey}",
                    $parentQuery->getModel()->getQualifiedKeyName()
                );
        });
    }
}
