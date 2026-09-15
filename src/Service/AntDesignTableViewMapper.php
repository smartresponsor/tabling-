<?php

declare(strict_types=1);

namespace App\Tabling\Service;

use App\Tabling\DTO\TableViewDTO;

final readonly class AntDesignTableViewMapper
{
    /** @return array<string, mixed> */
    public function map(TableViewDTO $view): array
    {
        $columns = [];
        foreach ($view->columns as $column) {
            $columns[$column->field] = array_filter([
                'show' => $column->visible,
                'order' => $column->order,
                'width' => $column->width,
                'fixed' => $column->pinned,
            ], static fn (mixed $value): bool => null !== $value);
        }

        return [
            'key' => $view->key,
            'label' => $view->label,
            'default' => $view->default,
            'columnsState' => $columns,
            'search' => $view->search,
            'filters' => array_map(
                static fn ($filter): array => ['field' => $filter->field, 'operator' => $filter->operator, 'value' => $filter->value],
                $view->filters,
            ),
            'sorts' => array_map(
                static fn ($sort): array => ['field' => $sort->field, 'direction' => $sort->direction],
                $view->sorts,
            ),
            'pageSize' => $view->pageSize,
            'meta' => $view->meta,
        ];
    }
}
