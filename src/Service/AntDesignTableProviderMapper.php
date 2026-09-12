<?php

declare(strict_types=1);

namespace App\Tabling\Service;

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
            'actions' => $definition->actions,
            'meta' => $definition->meta,
        ];
    }
}
