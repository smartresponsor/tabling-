<?php

declare(strict_types=1);

namespace App\Tabling\Service;

use App\Tabling\DTO\TableCapabilitiesDTO;
use App\Tabling\DTO\TableDefinitionDTO;
use App\Tabling\ServiceInterface\TableProviderMapperInterface;

final readonly class AntDesignTableProviderMapper implements TableProviderMapperInterface
{
    public function provider(): string
    {
        return 'ant-design-pro';
    }

    public function map(TableDefinitionDTO $definition): array
    {
        return [
            'provider' => $this->provider(),
            'name' => $definition->name,
            'columns' => array_map(static fn ($column): array => [
                'dataIndex' => $column->field,
                'title' => $column->label,
                'valueType' => $column->type,
                'sorter' => $column->sortable,
                'hideInTable' => !$column->visible,
                'search' => $column->filterable || $column->searchable,
            ], $definition->columns),
            'actions' => array_map(
                fn ($action): array => (new TableActionMetadataBuilder())->build($action),
                $definition->actions,
            ),
            'bulkActions' => array_map(
                fn ($action): array => (new TableActionMetadataBuilder())->build($action),
                $definition->bulkActions,
            ),
            'filters' => array_map(
                fn ($filter): array => (new TableFilterMetadataBuilder())->metadata($filter),
                $definition->filters,
            ),
            'defaultSorts' => array_map(
                static fn ($sort): array => ['field' => $sort->field, 'direction' => $sort->direction],
                $definition->defaultSorts,
            ),
            'facets' => array_map(static fn ($facet): array => [
                'field' => $facet->field,
                'label' => $facet->label,
                'limit' => $facet->limit,
                'includeMissing' => $facet->includeMissing,
                'excludeOwnFilter' => $facet->excludeOwnFilter,
            ], $definition->facets),
            'aggregations' => array_map(static fn ($aggregation): array => [
                'name' => $aggregation->name,
                'label' => $aggregation->label,
                'function' => $aggregation->function,
                'field' => $aggregation->field,
            ], $definition->aggregations),
            'groupBy' => $definition->groupBy,
            'exportPolicy' => (new TableExportPolicyMetadataBuilder())->metadata($definition),
            'capabilities' => ($definition->capabilities ?? new TableCapabilitiesDTO())->toArray(),
            'meta' => $definition->meta,
        ];
    }
}
