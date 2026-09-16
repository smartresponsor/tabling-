<?php

declare(strict_types=1);

namespace App\Tabling\Provider;

use App\Collectioning\DTO\CollectionDefinitionDTO;
use App\Tabling\Builder\TableActions;
use App\Tabling\Builder\TableAggregations;
use App\Tabling\Builder\TableColumns;
use App\Tabling\Builder\TableFacets;
use App\Tabling\Builder\TableFilters;
use App\Tabling\Builder\TableSorting;
use App\Tabling\DTO\TableCapabilitiesDTO;
use App\Tabling\DTO\TableDefinitionDTO;
use App\Tabling\DTO\TableExportPolicyDTO;
use App\Tabling\ServiceInterface\TableDefinitionProviderInterface;

abstract class AbstractTable implements TableDefinitionProviderInterface
{
    final public function definition(): TableDefinitionDTO
    {
        $columns = new TableColumns();
        $actions = new TableActions();
        $filters = new TableFilters();
        $facets = new TableFacets();
        $aggregations = new TableAggregations();
        $sorting = new TableSorting();

        $this->configureColumns($columns);
        $this->configureActions($actions);
        $this->configureFilters($filters);
        $this->configureFacets($facets);
        $this->configureAggregations($aggregations);
        $this->configureDefaultSorting($sorting);

        return new TableDefinitionDTO(
            $this->name(),
            $this->collection(),
            $columns->all(),
            $actions->row(),
            $this->meta(),
            $filters->all(),
            $actions->bulkActions(),
            $sorting->all(),
            $this->capabilities(),
            $facets->all(),
            $aggregations->all(),
            $aggregations->groups(),
            $this->exportPolicy(),
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

    protected function configureFacets(TableFacets $facets): void
    {
    }

    protected function configureAggregations(TableAggregations $aggregations): void
    {
    }

    protected function configureDefaultSorting(TableSorting $sorting): void
    {
    }

    protected function capabilities(): TableCapabilitiesDTO
    {
        return new TableCapabilitiesDTO();
    }

    protected function exportPolicy(): ?TableExportPolicyDTO
    {
        return null;
    }

    /** @return array<string, mixed> */
    protected function meta(): array
    {
        return [];
    }
}
