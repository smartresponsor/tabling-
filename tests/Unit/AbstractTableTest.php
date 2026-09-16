<?php

declare(strict_types=1);

namespace App\Tabling\Tests\Unit;

use App\Collectioning\DTO\CollectionDataScopeDTO;
use App\Collectioning\DTO\CollectionDefinitionDTO;
use App\Tabling\Builder\TableActions;
use App\Tabling\Builder\TableAggregations;
use App\Tabling\Builder\TableColumns;
use App\Tabling\Builder\TableFacets;
use App\Tabling\Builder\TableFilters;
use App\Tabling\Builder\TableSorting;
use App\Tabling\DTO\TableCapabilitiesDTO;
use App\Tabling\DTO\TableExportPolicyDTO;
use App\Tabling\Provider\AbstractTable;
use App\Tabling\Service\AntDesignTableProviderMapper;
use PHPUnit\Framework\TestCase;

final class AbstractTableTest extends TestCase
{
    public function testUsesSafeEmptyDefaultsForOptionalTableConfiguration(): void
    {
        $table = new class extends AbstractTable {
            protected function name(): string
            {
                return 'minimal';
            }

            protected function collection(): CollectionDefinitionDTO
            {
                return new CollectionDefinitionDTO(\stdClass::class, []);
            }
        };

        $definition = $table->definition();

        self::assertSame('minimal', $definition->name);
        self::assertSame([], $definition->columns);
        self::assertSame([], $definition->actions);
        self::assertSame([], $definition->filters);
        self::assertSame([], $definition->bulkActions);
        self::assertSame([], $definition->defaultSorts);
        self::assertSame([], $definition->meta);
        self::assertFalse($definition->capabilities?->rowSelection);
    }

    public function testCompilesPhpDeclarationIntoProviderNeutralDefinition(): void
    {
        $table = new class extends AbstractTable {
            protected function name(): string
            {
                return 'users';
            }

            protected function collection(): CollectionDefinitionDTO
            {
                return new CollectionDefinitionDTO(\stdClass::class, []);
            }

            protected function configureColumns(TableColumns $columns): void
            {
                $columns
                    ->text('name', sortable: true, searchable: true)
                    ->status()
                    ->dateTime('createdAt');
            }

            protected function configureActions(TableActions $actions): void
            {
                $actions
                    ->edit('crud_edit', ['id' => 42])
                    ->delete('crud_delete', ['id' => 42])
                    ->bulk('archive', 'Archive', 'crud_bulk_archive', permission: 'ARCHIVE');
            }

            protected function configureFilters(TableFilters $filters): void
            {
                $filters
                    ->text('q', 'Search', 'Search users')
                    ->select('status', [
                        ['label' => 'Active', 'value' => 'active'],
                    ], 'Status', 'Any status');
            }

            protected function configureFacets(TableFacets $facets): void
            {
                $facets->terms('status', 'Status', 10, includeMissing: true);
            }

            protected function configureAggregations(TableAggregations $aggregations): void
            {
                $aggregations
                    ->count('rows', 'Rows')
                    ->sum('totalAmount', 'amount', 'Total amount')
                    ->groupBy('status');
            }

            protected function configureDefaultSorting(TableSorting $sorting): void
            {
                $sorting->desc('createdAt')->asc('name');
            }

            protected function capabilities(): TableCapabilitiesDTO
            {
                return new TableCapabilitiesDTO(rowSelection: true, bulkActions: true, export: true);
            }

            protected function exportPolicy(): TableExportPolicyDTO
            {
                return new TableExportPolicyDTO(
                    formats: ['csv', 'json'],
                    scopes: [CollectionDataScopeDTO::FILTERED, CollectionDataScopeDTO::SELECTED],
                    defaultScope: CollectionDataScopeDTO::FILTERED,
                    permission: 'EXPORT_USERS',
                );
            }

            protected function meta(): array
            {
                return ['density' => 'compact'];
            }
        };

        $definition = $table->definition();

        self::assertSame('users', $definition->name);
        self::assertCount(3, $definition->columns);
        self::assertCount(2, $definition->actions);
        self::assertCount(1, $definition->bulkActions);
        self::assertCount(2, $definition->filters);
        self::assertCount(1, $definition->facets);
        self::assertCount(2, $definition->aggregations);
        self::assertSame(['status'], $definition->groupBy);
        self::assertSame('Name', $definition->columns[0]->label);
        self::assertSame('archive', $definition->bulkActions[0]->name);
        self::assertSame('createdAt', $definition->defaultSorts[0]->field);
        self::assertSame('desc', $definition->defaultSorts[0]->direction);
        self::assertTrue($definition->capabilities?->bulkActions);

        $ant = (new AntDesignTableProviderMapper())->map($definition);

        self::assertSame('q', $ant['filters'][0]['nameEntity']);
        self::assertSame('archive', $ant['bulkActions'][0]['operation']);
        self::assertSame('status', $ant['facets'][0]['field']);
        self::assertSame(10, $ant['facets'][0]['limit']);
        self::assertTrue($ant['facets'][0]['includeMissing']);
        self::assertSame('rows', $ant['aggregations'][0]['name']);
        self::assertSame('sum', $ant['aggregations'][1]['function']);
        self::assertSame(['status'], $ant['groupBy']);
        self::assertSame(['field' => 'createdAt', 'direction' => 'desc'], $ant['defaultSorts'][0]);
        self::assertTrue($ant['capabilities']['rowSelection']);
        self::assertTrue($ant['capabilities']['export']);
        self::assertSame(['csv', 'json'], $ant['exportPolicy']['formats']);
        self::assertSame(['filtered', 'selected'], $ant['exportPolicy']['scopes']);
        self::assertSame('filtered', $ant['exportPolicy']['defaultScope']);
        self::assertSame('EXPORT_USERS', $ant['exportPolicy']['permission']);
        self::assertSame('compact', $ant['meta']['density']);
    }
}
