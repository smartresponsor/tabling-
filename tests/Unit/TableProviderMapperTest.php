<?php

declare(strict_types=1);

namespace App\Tabling\Tests\Unit;

use App\Collectioning\DTO\CollectionDefinitionDTO;
use App\Tabling\DTO\TableActionDTO;
use App\Tabling\DTO\TableColumnDTO;
use App\Tabling\DTO\TableDefinitionDTO;
use App\Tabling\DTO\TableFacetDTO;
use App\Tabling\DTO\TableFilterDTO;
use App\Tabling\Service\AntDesignTableProviderMapper;
use App\Tabling\Service\PrimeReactTableProviderMapper;
use PHPUnit\Framework\TestCase;

final class TableProviderMapperTest extends TestCase
{
    public function testMapsOneDefinitionToBothUiProviders(): void
    {
        $definition = new TableDefinitionDTO(
            'vendor',
            new CollectionDefinitionDTO(\stdClass::class, []),
            [new TableColumnDTO('name', 'Name', 'text', true, true, true)],
            [new TableActionDTO('edit', 'Edit', 'crud_edit', ['id' => 42], 'EDIT')],
            [],
            [new TableFilterDTO('status', 'Status', 'select', 'Any status')],
            [new TableActionDTO('archive', 'Archive', 'crud_bulk_archive', [], 'ARCHIVE', 'bulk')],
            facets: [new TableFacetDTO('status', 'Status', 15, true)],
        );

        $ant = (new AntDesignTableProviderMapper())->map($definition);
        $prime = (new PrimeReactTableProviderMapper())->map($definition);

        self::assertSame('ant-design-pro', $ant['provider']);
        self::assertSame('name', $ant['columns'][0]['dataIndex']);
        self::assertTrue($ant['columns'][0]['sorter']);
        self::assertSame('edit', $ant['actions'][0]['operation']);
        self::assertSame('crud_edit', $ant['actions'][0]['routeName']);
        self::assertSame(['id' => 42], $ant['actions'][0]['routeParameters']);
        self::assertSame('EDIT', $ant['actions'][0]['permission']);
        self::assertSame('prime-react', $prime['provider']);
        self::assertSame('name', $prime['columns'][0]['field']);
        self::assertTrue($prime['columns'][0]['sortable']);
        self::assertSame($ant['actions'], $prime['actions']);
        self::assertSame('status', $ant['filters'][0]['nameEntity']);
        self::assertSame($ant['filters'], $prime['filters']);
        self::assertSame('archive', $ant['bulkActions'][0]['operation']);
        self::assertSame($ant['bulkActions'], $prime['bulkActions']);
        self::assertSame('status', $ant['facets'][0]['field']);
        self::assertSame(15, $ant['facets'][0]['limit']);
        self::assertTrue($ant['facets'][0]['includeMissing']);
        self::assertSame($ant['facets'], $prime['facets']);
        self::assertIsArray($prime['actions'][0]);
    }
}
