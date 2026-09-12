<?php

declare(strict_types=1);

namespace App\Tabling\Service;

use App\Tabling\DTO\TableDefinitionDTO;
use App\Tabling\ServiceInterface\TableProviderMapperInterface;

final readonly class PrimeReactTableProviderMapper implements TableProviderMapperInterface
{
    public function provider(): string
    {
        return 'prime-react';
    }

    public function map(TableDefinitionDTO $definition): array
    {
        return [
            'provider' => $this->provider(),
            'name' => $definition->name,
            'columns' => array_map(static fn ($column): array => [
                'field' => $column->field,
                'header' => $column->label,
                'dataType' => $column->type,
                'sortable' => $column->sortable,
                'filter' => $column->filterable,
                'hidden' => !$column->visible,
            ], $definition->columns),
            'actions' => $definition->actions,
            'meta' => $definition->meta,
        ];
    }
}
