<?php

declare(strict_types=1);

namespace App\Tabling\Tests\Unit;

use App\Collectioning\DTO\CollectionDefinitionDTO;
use App\Collectioning\DTO\CollectionFieldPolicyDTO;
use App\Collectioning\DTO\CollectionSortDTO;
use App\Tabling\DTO\TableDefinitionDTO;
use App\Tabling\Service\AntDesignCollectionQueryMapper;
use App\Tabling\Service\PrimeReactCollectionQueryMapper;
use App\Tabling\Service\TableCollectionQueryBuilder;
use PHPUnit\Framework\TestCase;

final class TableCollectionQueryMapperTest extends TestCase
{
    public function testMapsAntDesignServerStateIntoCollectionQuery(): void
    {
        $table = $this->table();
        $query = (new AntDesignCollectionQueryMapper(new TableCollectionQueryBuilder()))->map([
            'current' => 3,
            'pageSize' => 50,
            'q' => '  Alice  ',
            'filters' => ['status' => 'active', 'secret' => 'ignored'],
            'sorter' => ['name' => 'ascend', 'secret' => 'descend'],
            'fields' => ['name', 'status', 'secret'],
        ], $table);

        self::assertSame(3, $query->page->number);
        self::assertSame(50, $query->page->size);
        self::assertSame('Alice', $query->search);
        self::assertCount(1, $query->filters);
        self::assertSame('status', $query->filters[0]->field);
        self::assertCount(1, $query->sorts);
        self::assertSame('name', $query->sorts[0]->field);
        self::assertSame(['name', 'status'], $query->fields);
    }

    public function testMapsAntDesignMultiValueFilterWhenCollectionPolicyAllowsInOperator(): void
    {
        $query = (new AntDesignCollectionQueryMapper(new TableCollectionQueryBuilder()))->map([
            'filters' => ['status' => ['active', 'pending']],
        ], $this->table());

        self::assertCount(1, $query->filters);
        self::assertSame('status', $query->filters[0]->field);
        self::assertSame('in', $query->filters[0]->operator);
        self::assertSame(['active', 'pending'], $query->filters[0]->value);
    }

    public function testMapsPrimeReactInMatchModeWithoutDroppingArrayValue(): void
    {
        $query = (new PrimeReactCollectionQueryMapper(new TableCollectionQueryBuilder()))->map([
            'filters' => [
                'status' => [
                    'value' => ['active', 'pending'],
                    'matchMode' => 'in',
                ],
            ],
        ], $this->table());

        self::assertCount(1, $query->filters);
        self::assertSame('status', $query->filters[0]->field);
        self::assertSame('in', $query->filters[0]->operator);
        self::assertSame(['active', 'pending'], $query->filters[0]->value);
    }

    public function testRejectsProviderMultiValueFilterWhenCollectionPolicyDoesNotAllowInOperator(): void
    {
        $query = (new AntDesignCollectionQueryMapper(new TableCollectionQueryBuilder()))->map([
            'filters' => ['name' => ['alice', 'bob']],
        ], $this->table());

        self::assertSame([], $query->filters);
    }

    public function testRejectsUnsupportedExplicitPrimeReactMatchModeInsteadOfReinterpretingIt(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported PrimeReact match mode "contains" for field "status".');

        (new PrimeReactCollectionQueryMapper(new TableCollectionQueryBuilder()))->map([
            'filters' => [
                'status' => [
                    'value' => 'active',
                    'matchMode' => 'contains',
                ],
            ],
        ], $this->table());
    }

    public function testMapsPrimeReactLazyStateAndUsesDefaultSorts(): void
    {
        $table = $this->table();
        $query = (new PrimeReactCollectionQueryMapper(new TableCollectionQueryBuilder()))->map([
            'first' => 40,
            'rows' => 20,
            'globalFilter' => 'bob',
            'filters' => [
                'status' => ['value' => 'pending'],
                'secret' => ['value' => 'ignored'],
            ],
        ], $table);

        self::assertSame(3, $query->page->number);
        self::assertSame(20, $query->page->size);
        self::assertSame('bob', $query->search);
        self::assertSame('status', $query->filters[0]->field);
        self::assertSame('createdAt', $query->sorts[0]->field);
        self::assertSame('desc', $query->sorts[0]->direction);
    }

    public function testPrimeReactExplicitMultiSortOverridesDefaults(): void
    {
        $query = (new PrimeReactCollectionQueryMapper(new TableCollectionQueryBuilder()))->map([
            'multiSortMeta' => [
                ['field' => 'name', 'order' => 1],
                ['field' => 'createdAt', 'order' => -1],
            ],
        ], $this->table());

        self::assertSame('name', $query->sorts[0]->field);
        self::assertSame('asc', $query->sorts[0]->direction);
        self::assertSame('createdAt', $query->sorts[1]->field);
        self::assertSame('desc', $query->sorts[1]->direction);
    }

    private function table(): TableDefinitionDTO
    {
        return new TableDefinitionDTO(
            'users',
            new CollectionDefinitionDTO(\stdClass::class, [
                new CollectionFieldPolicyDTO('name', searchable: true, sortable: true),
                new CollectionFieldPolicyDTO('status', filterable: true, filterOperators: ['eq', 'in']),
                new CollectionFieldPolicyDTO('createdAt', sortable: true),
                new CollectionFieldPolicyDTO('secret', projectable: false),
            ], 25, 100),
            [],
            defaultSorts: [new CollectionSortDTO('createdAt', 'desc')],
        );
    }
}
