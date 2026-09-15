<?php

declare(strict_types=1);

namespace App\Tabling\Tests\Unit;

use App\Collectioning\DTO\CollectionDefinitionDTO;
use App\Tabling\Builder\TableActions;
use App\Tabling\Builder\TableColumns;
use App\Tabling\Builder\TableFilters;
use App\Tabling\Provider\AbstractTable;
use App\Tabling\Service\AntDesignTableProviderMapper;
use PHPUnit\Framework\TestCase;

final class AbstractTableTest extends TestCase
{
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
        self::assertSame('Name', $definition->columns[0]->label);
        self::assertSame('archive', $definition->bulkActions[0]->name);

        $ant = (new AntDesignTableProviderMapper())->map($definition);

        self::assertSame('q', $ant['filters'][0]['nameEntity']);
        self::assertSame('archive', $ant['bulkActions'][0]['operation']);
        self::assertSame('compact', $ant['meta']['density']);
    }
}
