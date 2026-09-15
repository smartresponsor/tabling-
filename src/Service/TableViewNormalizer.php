<?php

declare(strict_types=1);

namespace App\Tabling\Service;

use App\Collectioning\DTO\CollectionFilterDTO;
use App\Collectioning\DTO\CollectionSortDTO;
use App\Tabling\DTO\TableColumnStateDTO;
use App\Tabling\DTO\TableDefinitionDTO;
use App\Tabling\DTO\TableViewDTO;

final readonly class TableViewNormalizer
{
    public function normalize(TableDefinitionDTO $table, TableViewDTO $view): TableViewDTO
    {
        $columnFields = [];
        foreach ($table->columns as $column) {
            $columnFields[$column->field] = true;
        }

        $policies = [];
        foreach ($table->collection->fields as $policy) {
            $policies[$policy->field] = $policy;
        }

        $columns = [];
        foreach ($view->columns as $column) {
            if (!isset($columnFields[$column->field])) {
                continue;
            }
            $pinned = in_array($column->pinned, ['left', 'right'], true) ? $column->pinned : null;
            $columns[] = new TableColumnStateDTO(
                $column->field,
                $column->visible,
                null === $column->order ? null : max(0, $column->order),
                null === $column->width ? null : max(40, $column->width),
                $pinned,
            );
        }

        $filters = [];
        foreach ($view->filters as $filter) {
            $policy = $policies[$filter->field] ?? null;
            if (null === $policy || !$policy->filterable || !in_array($filter->operator, $policy->filterOperators, true)) {
                continue;
            }
            $filters[] = new CollectionFilterDTO($filter->field, $filter->operator, $filter->value);
        }

        $sorts = [];
        foreach ($view->sorts as $sort) {
            $policy = $policies[$sort->field] ?? null;
            $direction = strtolower($sort->direction);
            if (null === $policy || !$policy->sortable || !in_array($direction, ['asc', 'desc'], true)) {
                continue;
            }
            $sorts[] = new CollectionSortDTO($sort->field, $direction);
        }

        $search = null === $view->search ? null : trim($view->search);
        $pageSize = null === $view->pageSize
            ? null
            : max(1, min($table->collection->maxPageSize, $view->pageSize));

        return new TableViewDTO(
            $view->key,
            $view->label,
            $columns,
            '' === $search ? null : $search,
            $filters,
            $sorts,
            $pageSize,
            $view->default,
            $view->meta,
        );
    }
}
