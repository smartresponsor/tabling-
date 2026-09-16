<?php

declare(strict_types=1);

namespace App\Tabling\Tests\Unit;

use App\Collectioning\DTO\CollectionAggregationDTO;
use App\Collectioning\DTO\CollectionAggregationResultDTO;
use App\Collectioning\DTO\CollectionAggregationRowDTO;
use App\Collectioning\DTO\CollectionDefinitionDTO;
use App\Collectioning\DTO\CollectionPageDTO;
use App\Collectioning\DTO\CollectionQueryDTO;
use App\Collectioning\ServiceInterface\CollectionAggregationProcessorInterface;
use App\Tabling\DTO\TableAggregationDTO;
use App\Tabling\DTO\TableDefinitionDTO;
use App\Tabling\Service\TableAggregationService;
use PHPUnit\Framework\TestCase;

final class TableAggregationServiceTest extends TestCase
{
    public function testBridgesTableAggregationsToCollectioning(): void
    {
        $processor = new class implements CollectionAggregationProcessorInterface {
            public ?CollectionDefinitionDTO $definition = null;
            public ?CollectionQueryDTO $query = null;

            /** @var list<CollectionAggregationDTO> */
            public array $aggregations = [];

            /** @var list<string> */
            public array $groupBy = [];

            public function process(CollectionDefinitionDTO $definition, CollectionQueryDTO $query, array $aggregations, array $groupBy = []): CollectionAggregationResultDTO
            {
                $this->definition = $definition;
                $this->query = $query;
                $this->aggregations = $aggregations;
                $this->groupBy = $groupBy;

                return new CollectionAggregationResultDTO([
                    new CollectionAggregationRowDTO(['status' => 'active'], ['rows' => 3]),
                ], $groupBy);
            }
        };

        $collection = new CollectionDefinitionDTO(\stdClass::class, []);
        $table = new TableDefinitionDTO(
            'users',
            $collection,
            [],
            aggregations: [
                new TableAggregationDTO('rows', 'Rows', 'count'),
                new TableAggregationDTO('totalAmount', 'Total amount', 'sum', 'amount'),
            ],
            groupBy: ['status'],
        );
        $query = new CollectionQueryDTO(new CollectionPageDTO(1, 25));

        $result = (new TableAggregationService($processor))->load($table, $query);

        self::assertSame($collection, $processor->definition);
        self::assertSame($query, $processor->query);
        self::assertCount(2, $processor->aggregations);
        self::assertSame('count', $processor->aggregations[0]->function);
        self::assertSame('amount', $processor->aggregations[1]->field);
        self::assertSame(['status'], $processor->groupBy);
        self::assertSame(3, $result->rows[0]->values['rows']);
    }

    public function testSkipsProcessorWhenTableHasNoAggregations(): void
    {
        $processor = $this->createMock(CollectionAggregationProcessorInterface::class);
        $processor->expects(self::never())->method('process');

        $table = new TableDefinitionDTO('users', new CollectionDefinitionDTO(\stdClass::class, []), [], groupBy: ['status']);
        $query = new CollectionQueryDTO(new CollectionPageDTO(1, 25));

        $result = (new TableAggregationService($processor))->load($table, $query);

        self::assertSame([], $result->rows);
        self::assertSame(['status'], $result->groupBy);
    }
}
