<?php

declare(strict_types=1);

namespace App\Tabling\Tests\Unit;

use App\Collectioning\DTO\CollectionDefinitionDTO;
use App\Collectioning\DTO\CollectionFacetBucketDTO;
use App\Collectioning\DTO\CollectionFacetDTO;
use App\Collectioning\DTO\CollectionFacetResultDTO;
use App\Collectioning\DTO\CollectionPageDTO;
use App\Collectioning\DTO\CollectionQueryDTO;
use App\Collectioning\ServiceInterface\CollectionFacetProcessorInterface;
use App\Tabling\DTO\TableDefinitionDTO;
use App\Tabling\DTO\TableFacetDTO;
use App\Tabling\Service\TableFacetService;
use PHPUnit\Framework\TestCase;

final class TableFacetServiceTest extends TestCase
{
    public function testBridgesTableFacetDeclarationsToCollectioning(): void
    {
        $processor = new class implements CollectionFacetProcessorInterface {
            public ?CollectionDefinitionDTO $definition = null;
            public ?CollectionQueryDTO $query = null;

            /** @var list<CollectionFacetDTO> */
            public array $facets = [];

            public function process(CollectionDefinitionDTO $definition, CollectionQueryDTO $query, array $facets): array
            {
                $this->definition = $definition;
                $this->query = $query;
                $this->facets = $facets;

                return [
                    new CollectionFacetResultDTO('status', [
                        new CollectionFacetBucketDTO('active', 3),
                    ]),
                ];
            }
        };

        $definition = new CollectionDefinitionDTO(\stdClass::class, []);
        $table = new TableDefinitionDTO(
            'users',
            $definition,
            [],
            facets: [
                new TableFacetDTO('status', 'Status', 12, true, false),
            ],
        );
        $query = new CollectionQueryDTO(new CollectionPageDTO(1, 25));

        $results = (new TableFacetService($processor))->load($table, $query);

        self::assertSame($definition, $processor->definition);
        self::assertSame($query, $processor->query);
        self::assertCount(1, $processor->facets);
        self::assertSame('status', $processor->facets[0]->field);
        self::assertSame(12, $processor->facets[0]->limit);
        self::assertTrue($processor->facets[0]->includeMissing);
        self::assertFalse($processor->facets[0]->excludeOwnFilter);
        self::assertSame('active', $results[0]->buckets[0]->value);
        self::assertSame(3, $results[0]->buckets[0]->count);
    }

    public function testSkipsProcessorWhenTableHasNoFacets(): void
    {
        $processor = $this->createMock(CollectionFacetProcessorInterface::class);
        $processor->expects(self::never())->method('process');

        $table = new TableDefinitionDTO('users', new CollectionDefinitionDTO(\stdClass::class, []), []);
        $query = new CollectionQueryDTO(new CollectionPageDTO(1, 25));

        self::assertSame([], (new TableFacetService($processor))->load($table, $query));
    }
}
