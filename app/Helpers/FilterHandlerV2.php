<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FilterHandlerV2
{
    private $filterMethods = [
        'EQUALS' => 'applyEquals',
        'GREATER_THAN' => 'applyGreaterThan',
        'LESS_THAN' => 'applyLessThan',
        'ILIKE' => 'applyIlike',
        'VALUE_RANGE' => 'applyValueRange',
        'NOT_NULL' => 'applyNotNull',
        'DATE_TIME_RANGE' => 'applyDateTimeRange',
        'DATE_RANGE' => 'applyDateRange',
        'IN_ARRAY' => 'applyInArray',
        'NOT_IN_ARRAY' => 'applyNotInArray',
        'NULL' => 'applyNull',
        'RELATION_DOESNT_EXIST' => 'applyRelationDoesntExist',
        'COLUMN_EQUALS' => 'applyColumnEquals',
        'COLUMN_NOT_EQUAL' => 'applyColumnNotEqual',
        'FIXED' => 'applyFixed',
        'LENGTH_EQUALS' => 'applyLengthEquals',
        'LENGTH_GREATER_THAN' => 'applyLengthGreaterThan',
        'LENGTH_LESS_THAN' => 'applyLengthLessThan',
    ];

    /**
     * Apply a series of filters to the given query based on the provided order params, which may include nested relationships.
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    

    public function applyFilter(Builder $query, array $filterParams)
    {
        foreach ($filterParams as $filter) {
            if (isset($filter['group'])) {
                $this->handleGroup($query, $filter);
            } else {
                $this->applyCondition($query, $filter);
            }
        }

        return $query;
    }

    private function handleGroup(Builder $query, array $filterParams)
    {
        $group = $filterParams['group'];

        $query->where(function ($q) use ($group) {
            foreach ($group as $condition) {
                $this->applyGroupCondition($q, $condition);
            }
        });
    }

    private function applyGroupCondition(Builder $query, array $condition)
    {
        $method = isset($condition['operator']) && $condition['operator'] === 'OR' ? 'orWhere' : 'where';

        if (isset($condition['group'])) {
            $query->$method(function ($q) use ($condition) {
                $this->handleGroup($q, $condition);
            });
        } else {
            $this->applyCondition($query, $condition);
        }
    }

    private function applyCondition(Builder $query, array $condition)
    {
        $field = strtolower($condition['field']);
        $filterMethod = $this->filterMethods[$condition['filterType']] ?? null;
        $operator = $condition['operator'] ?? 'AND';
        if ($filterMethod && method_exists($this, $filterMethod)) {
            if ($operator === 'OR') {
                $query->orWhere(function ($q) use ($filterMethod, $condition, $field) {
                    $this->$filterMethod($q, $field, $condition['filterValue'] ?? null);
                });
            } else {
                $this->$filterMethod($query, $field, $condition['filterValue'] ?? null);
            }
        }
    }


    private function handleRelation($query, $field, $callback)
    {
        if (strpos($field, '.') !== false) {
            list($relation, $field) = explode('.', $field, 2);
            $query->whereHas($relation, function ($q) use ($field, $callback) {
                $callback($q, $field);
            });
        } else {
            $callback($query, $field);
        }
    }

    private function applyEquals($query, $field, $value)
    {
        $this->handleRelation($query, $field, function ($q, $field) use ($value) {
            $q->where($field, '=', $value);
        });
    }

    private function applyGreaterThan($query, $field, $value)
    {
        $this->handleRelation($query, $field, function ($q, $field) use ($value) {
            $q->where($field, '>', $value);
        });
    }

    private function applyLessThan($query, $field, $value)
    {
        $this->handleRelation($query, $field, function ($q, $field) use ($value) {
            $q->where($field, '<', $value);
        });
    }

    private function applyIlike($query, $field, $value)
    {
        $this->handleRelation($query, $field, function ($q, $field) use ($value) {
            // Para MySQL, use LIKE e COLLATE para case-insensitive e acentuação-insensitive (se disponível)
            $q->where($field, 'LIKE', '%' . $value . '%');
        });
    }

    private function applyValueRange($query, $field, $value)
    {
        $this->handleRelation($query, $field, function ($q, $field) use ($value) {
            if (isset($value['min'])) {
                $q->where($field, '>=', $value['min']);
            }
            if (isset($value['max'])) {
                $q->where($field, '<=', $value['max']);
            }
        });
    }

    private function applyNotNull($query, $field)
    {
        $this->handleRelation($query, $field, function ($q, $field) {
            $q->whereNotNull($field);
        });
    }

    private function applyDateTimeRange($query, $field, $value)
    {
        $this->handleRelation($query, $field, function ($q, $field) use ($value) {
            $startDate = Carbon::parse($value['startDate'], 'America/Sao_Paulo')->setTimezone('UTC')->toDateTimeString();
            $endDate = Carbon::parse($value['endDate'], 'America/Sao_Paulo')->setTimezone('UTC')->toDateTimeString();

            $q->whereBetween($field, [$startDate, $endDate]);
        });
    }

    private function applyDateRange($query, $field, $value)
    {
        $this->handleRelation($query, $field, function ($q, $field) use ($value) {
            $q->whereBetween($field, [$value['startDate'], $value['endDate']]);
        });
    }

    private function applyInArray($query, $field, $value)
    {
        $this->handleRelation($query, $field, function ($q, $field) use ($value) {
            $q->whereIn($field, $value);
        });
    }

    private function applyNotInArray($query, $field, $value)
    {
        $this->handleRelation($query, $field, function ($q, $field) use ($value) {
            $q->whereNotIn($field, $value);
        });
    }

    private function applyNull($query, $field)
    {
        $this->handleRelation($query, $field, function ($q, $field) {
            $q->whereNull($field);
        });
    }

    private function applyRelationDoesntExist($query, $field)
    {
        $this->handleRelation($query, $field, function ($q, $field) {
            $q->doesntHave($field);
        });
    }

    private function applyColumnEquals($query, $field, $value)
    {
        $this->handleRelation($query, $field, function ($q, $field) use ($value) {
            $q->whereColumn($field, '=', $value);
        });
    }

    private function applyColumnNotEqual($query, $field, $value)
    {
        $this->handleRelation($query, $field, function ($q, $field) use ($value) {
            $q->whereColumn($field, '!=', $value);
        });
    }

    private function applyFixed($query, $field, $value)
    {
        $this->handleRelation($query, $field, function ($q, $field) use ($value) {
            $q->where($field, '!=', $value);
        });
    }

    private function applyLengthEquals(Builder $query, $field, $length)
    {
        $query->whereRaw('LENGTH(' . $this->escapeField($field) . ') = ?', [$length]);
    }

    private function applyLengthGreaterThan(Builder $query, $field, $length)
    {
        $query->whereRaw('LENGTH(' . $this->escapeField($field) . ') > ?', [$length]);
    }

    private function applyLengthLessThan(Builder $query, $field, $length)
    {
        $query->whereRaw('LENGTH(' . $this->escapeField($field) . ') < ?', [$length]);
    }
    private function escapeField($field)
    {
        return preg_replace('/[^a-zA-Z0-9_.]/', '', $field);
    }

    public function applyOrder($query, $orderByParams)
    {
        $joinedTables = [];

        foreach ($orderByParams as $field => $direction) {
            $field = strtolower($field);
            $dotPosition = strrpos($field, '.');
            if ($dotPosition !== false) {
                $relationship = substr($field, 0, $dotPosition);
                $column = substr($field, $dotPosition + 1);

                if (method_exists($query->getModel(), $relationship)) {
                    if (!in_array($relationship, $joinedTables)) {
                        $joinedTables[] = $relationship;
                        $relationInstance = $query->getModel()->$relationship();
                        $relatedModel = $relationInstance->getRelated();
                        $relatedTable = $relatedModel->getTable();

                        if ($relationInstance instanceof \Illuminate\Database\Eloquent\Relations\HasOne) {
                            $foreignKey = $relationInstance->getForeignKeyName();
                            $localKey = $relationInstance->getLocalKeyName();
                        } elseif ($relationInstance instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo) {
                            $foreignKey = $relationInstance->getOwnerKeyName();
                            $localKey = $relationInstance->getForeignKeyName();
                        }

                        $query->leftJoin($relatedTable, $query->getModel()->getTable() . '.' . $localKey, '=', $relatedTable . '.' . $foreignKey)
                            ->select($query->getModel()->getTable() . '.*');
                    }
                    $query->orderBy($relatedTable . '.' . $column, $direction);
                }
            } else {
                $query->orderBy($field, $direction);
            }
        }
        return $query;
    }
}
