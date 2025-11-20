<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class BaseController extends Controller
{
    protected function applySorting(
        Builder $query,
        Request $request,
        string $defaultSort = 'id',
        string $defaultOrder = 'desc',
        array $allowedSortFields = []
    ): Builder {
        $sortBy = $request->get('sort_by', $defaultSort);
        $sortOrder = $request->get('sort_order', $defaultOrder);

        if (!empty($allowedSortFields) && !in_array($sortBy, $allowedSortFields)) {
            $sortBy = $defaultSort;
        }

        if (!in_array(strtolower($sortOrder), ['asc', 'desc'])) {
            $sortOrder = $defaultOrder;
        }

        $query->orderBy($sortBy, $sortOrder);

        return $query;
    }
}
