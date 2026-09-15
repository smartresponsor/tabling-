<?php

declare(strict_types=1);

namespace App\Tabling\Provider;

use App\Collectioning\DTO\CollectionDefinitionDTO;
use App\Tabling\Builder\TableActions;
use App\Tabling\Builder\TableColumns;
use App\Tabling\Builder\TableFilters;
use App\Tabling\DTO\TableDefinitionDTO;
use App\Tabling\ServiceInterface\TableDefinitionProviderInterface;

abstract class AbstractTable implements TableDefinitionProviderInterface
{
    final public function definition(): TableDefinitionDTO
    {
        $columns = new TableColumns();
        $actions = new TableActions();
        $filters = new TableFilters();

        $this->configureColumns($columns);
        $this->configureActions($actions);
        $this->configureFilters($filters);

        return new TableDefinitionDTO(
            $this->name(),
            $this->collection(),
            $columns->all(),
            $actions->row(),
            $this->meta(),
            $filters->all(),
            $actions->bulkActions(),
        );
    }

    abstract protected function name(): string;

    abstract protected function collection(): CollectionDefinitionDTO;

    protected function configureColumns(TableColumns $columns): void
    {
    }

    protected function configureActions(TableActions $actions): void
    {
    }

    protected function configureFilters(TableFilters $filters): void
    {
    }

    /** @return array<string, mixed> */
    protected function meta(): array
    {
        return [];
    }
}
