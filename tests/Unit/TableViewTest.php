<?php

declare(strict_types=1);

namespace App\Tabling\Tests\Unit;

use App\Collectioning\DTO\CollectionDefinitionDTO;
use App\Collectioning\DTO\CollectionFieldPolicyDTO;
use App\Collectioning\DTO\CollectionFilterDTO;
use App\Collectioning\DTO\CollectionSortDTO;
use App\Tabling\DTO\TableColumnDTO;
use App\Tabling\DTO\TableColumnStateDTO;
use App\Tabling\DTO\TableDefinitionDTO;
use App\Tabling\DTO\TableViewDTO;
use App\Tabling\Service\AntDesignTableViewMapper;
use App\Tabling\Service\PrimeReactTableViewMapper;
use App\Tabling\Service\TableViewNormalizer;
use PHPUnit\Framework\TestCase;

final class TableViewTest extends TestCase
{
    public function testNormalizesSavedViewAgainstCurrentTablePolicies(): void
    {
        $table = $this->table();
        $view = new TableViewDTO(
            'ops',
            'Operations',
            [
                new TableColumnStateDTO('status', false, 1, 120, 'left'),
                new TableColumnStateDTO('missing', true, 0, 20, 'middle'),
            ],
            '  alice  ',
            [
                new CollectionFilterDTO('status', 'eq', 'active'),
                new CollectionFilterDTO('secret', 'eq', 'ignored'),
            ],
            [
                new CollectionSortDTO('name', 'DESC'),
                new CollectionSortDTO('secret', 'asc'),
            ],
            500,
            true,
        );

        $normalized = (new TableViewNormalizer())->normalize($table, $view);

        self::assertSame('alice', $normalized->search);
        self::assertSame(100, $normalized->pageSize);
        self::assertCount(1, $normalized->columns);
        self::assertSame('left', $normalized->columns[0]->pinned);
        self::assertCount(1, $normalized->filters);
        self::assertCount(1, $normalized->sorts);
        self::assertSame('desc', $normalized->sorts[0]->direction);
    }

    public function testMapsOneNormalizedViewToBothProviders(): void
    {
        $view = (new TableViewNormalizer())->normalize($this->table(), new TableViewDTO(
            'personal',
            'Personal',
            [
                new TableColumnStateDTO('name', true, 1, 240),
                new TableColumnStateDTO('status', false, 0, 100),
            ],
            'bob',
            [new CollectionFilterDTO('status', 'eq', 'pending')],
            [new CollectionSortDTO('name', 'asc')],
            50,
        ));

        $ant = (new AntDesignTableViewMapper())->map($view);
        $prime = (new PrimeReactTableViewMapper())->map($view);

        self::assertFalse($ant['columnsState']['status']['show']);
        self::assertSame(240, $ant['columnsState']['name']['width']);
        self::assertSame(['status', 'name'], $prime['columnOrder']);
        self::assertSame(['status'], $prime['hiddenColumns']);
        self::assertSame(50, $prime['rows']);
        self::assertSame(1, $prime['multiSortMeta'][0]['order']);
    }

    private function table(): TableDefinitionDTO
    {
        return new TableDefinitionDTO(
            'users',
            new CollectionDefinitionDTO(\stdClass::class, [
                new CollectionFieldPolicyDTO('name', searchable: true, sortable: true),
                new CollectionFieldPolicyDTO('status', filterable: true),
                new CollectionFieldPolicyDTO('secret'),
            ], 25, 100),
            [
                new TableColumnDTO('name', 'Name', sortable: true, searchable: true),
                new TableColumnDTO('status', 'Status', filterable: true),
            ],
        );
    }
}
