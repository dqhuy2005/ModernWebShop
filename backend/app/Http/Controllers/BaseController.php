<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class BaseController extends Controller
{
    /**
     * Apply filters to query efficiently
     * Only applies filters that have values
     *
     * @param Builder $query
     * @param Request $request
     * @param array $filterConfig
     * @return Builder
     */
    protected function applyFilters(Builder $query, Request $request, array $filterConfig): Builder
    {
        foreach ($filterConfig as $param => $config) {
            // Skip if request doesn't have this parameter or it's empty
            if (!$request->filled($param)) {
                continue;
            }

            $value = $request->input($param);
            $type = $config['type'] ?? 'exact';
            $column = $config['column'] ?? $param;

            // Apply filter based on type
            switch ($type) {
                case 'exact':
                    // Exact match: status = 'active'
                    $query->where($column, $value);
                    break;

                case 'like':
                    // LIKE search: name LIKE '%value%'
                    $query->where($column, 'like', "%{$value}%");
                    break;

                case 'in':
                    // IN clause: id IN (1,2,3)
                    $values = is_array($value) ? $value : explode(',', $value);
                    $query->whereIn($column, $values);
                    break;

                case 'between':
                    // BETWEEN: price BETWEEN min AND max
                    if (isset($config['min']) && $request->filled($config['min'])) {
                        $query->where($column, '>=', $request->input($config['min']));
                    }
                    if (isset($config['max']) && $request->filled($config['max'])) {
                        $query->where($column, '<=', $request->input($config['max']));
                    }
                    break;

                case 'date_range':
                    // Date range: created_at >= date_from AND created_at <= date_to
                    if (isset($config['from']) && $request->filled($config['from'])) {
                        $query->whereDate($column, '>=', $request->input($config['from']));
                    }
                    if (isset($config['to']) && $request->filled($config['to'])) {
                        $query->whereDate($column, '<=', $request->input($config['to']));
                    }
                    break;

                case 'boolean':
                    // Boolean: status = 1 or 0
                    $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    $query->where($column, $boolValue);
                    break;

                case 'relation':
                    // Relation search: whereHas('category', ...)
                    $relation = $config['relation'];
                    $relationColumn = $config['relation_column'];
                    $query->whereHas($relation, function ($q) use ($relationColumn, $value) {
                        $q->where($relationColumn, 'like', "%{$value}%");
                    });
                    break;

                case 'custom':
                    // Custom callback
                    if (isset($config['callback']) && is_callable($config['callback'])) {
                        $config['callback']($query, $value, $request);
                    }
                    break;
            }
        }

        return $query;
    }

    /**
     * Apply search to multiple columns
     *
     * @param Builder $query
     * @param Request $request
     * @param array $searchColumns
     * @param string $searchParam
     * @return Builder
     */
    protected function applySearch(Builder $query, Request $request, array $searchColumns, string $searchParam = 'search'): Builder
    {
        if (!$request->filled($searchParam)) {
            return $query;
        }

        $searchTerm = $request->input($searchParam);

        $query->where(function ($q) use ($searchColumns, $searchTerm) {
            foreach ($searchColumns as $column) {
                // Check if it's a relation search
                if (strpos($column, '.') !== false) {
                    [$relation, $relationColumn] = explode('.', $column, 2);
                    $q->orWhereHas($relation, function ($relationQuery) use ($relationColumn, $searchTerm) {
                        $relationQuery->where($relationColumn, 'like', "%{$searchTerm}%");
                    });
                } else {
                    $q->orWhere($column, 'like', "%{$searchTerm}%");
                }
            }
        });

        return $query;
    }

    /**
     * Apply sorting
     *
     * @param Builder $query
     * @param Request $request
     * @param string $defaultSort
     * @param string $defaultOrder
     * @param array $allowedSortFields
     * @return Builder
     */
    protected function applySorting(
        Builder $query,
        Request $request,
        string $defaultSort = 'id',
        string $defaultOrder = 'desc',
        array $allowedSortFields = []
    ): Builder {
        $sortBy = $request->get('sort_by', $defaultSort);
        $sortOrder = $request->get('sort_order', $defaultOrder);

        // Validate sort field
        if (!empty($allowedSortFields) && !in_array($sortBy, $allowedSortFields)) {
            $sortBy = $defaultSort;
        }

        // Validate sort order
        if (!in_array(strtolower($sortOrder), ['asc', 'desc'])) {
            $sortOrder = $defaultOrder;
        }

        $query->orderBy($sortBy, $sortOrder);

        return $query;
    }
}
