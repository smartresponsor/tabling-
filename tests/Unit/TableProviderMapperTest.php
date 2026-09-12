<?php

declare(strict_types=1);

namespace App\Tabling\Tests\Unit;

use App\Collectioning\DTO\CollectionDefinitionDTO;
use App\Tabling\DTO\TableColumnDTO;
use App\Tabling\DTO\TableDefinitionDTO;
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
        );

        $ant = (new AntDesignTableProviderMapper())->map($definition);
        $prime = (new PrimeReactTableProviderMapper())->map($definition);

        self::assertSame('ant-design-pro', $ant['provider']);
        self::assertSame('name', $ant['columns'][0]['dataIndex']);
        self::assertTrue($ant['columns'][0]['sorter']);
        self::assertSame('prime-react', $prime['provider']);
        self::assertSame('name', $prime['columns'][0]['field']);
        self::assertTrue($prime['columns'][0]['sortable']);
    }
}
