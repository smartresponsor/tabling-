<?php

declare(strict_types=1);

namespace App\Tabling\Service;

use App\Tabling\DTO\TableViewDTO;

final readonly class PrimeReactTableViewMapper
{
    /** @return array<string, mixed> */
    public function map(TableViewDTO $view): array
    {
        $ordered = $view->columns;
        usort($ordered, static fn ($left, $right): int => ($left->order ?? PHP_INT_MAX) <=> ($right->order ?? PHP_INT_MAX));

        return [
            'key' => $view->key,
            'label' => $view->label,
            'default' => $view->default,
            'columnOrder' => array_map(static fn ($column): string => $column->field, $ordered),
            'hiddenColumns' => array_values(array_map(
                static fn ($column): string => $column->field,
                array_filter($view->columns, static fn ($column): bool => !$column->visible),
            )),
            'columnWidths' => array_filter(
                array_column(array_map(
                    static fn ($column): array => ['field' => $column->field, 'width' => $column->width],
                    $view->columns,
                ), 'width', 'field'),
                static fn (mixed $value): bool => null !== $value,
            ),
            'globalFilter' => $view->search,
            'filters' => array_map(
                static fn ($filter): array => ['field' => $filter->field, 'operator' => $filter->operator, 'value' => $filter->value],
                $view->filters,
            ),
            'multiSortMeta' => array_map(
                static fn ($sort): array => ['field' => $sort->field, 'order' => 'desc' === strtolower($sort->direction) ? -1 : 1],
                $view->sorts,
            ),
            'rows' => $view->pageSize,
            'meta' => $view->meta,
        ];
    }
}
