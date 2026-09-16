<?php

declare(strict_types=1);

namespace App\Tabling\Service;

use App\Collectioning\DTO\CollectionAggregationDTO;
use App\Collectioning\DTO\CollectionAggregationResultDTO;
use App\Collectioning\DTO\CollectionQueryDTO;
use App\Collectioning\ServiceInterface\CollectionAggregationProcessorInterface;
use App\Tabling\DTO\TableDefinitionDTO;

final readonly class TableAggregationService
{
    public function __construct(private CollectionAggregationProcessorInterface $aggregationProcessor)
    {
    }

    public function load(TableDefinitionDTO $table, CollectionQueryDTO $query): CollectionAggregationResultDTO
    {
        $aggregations = array_map(
            static fn ($aggregation): CollectionAggregationDTO => new CollectionAggregationDTO(
                $aggregation->name,
                $aggregation->function,
                $aggregation->field,
            ),
            $table->aggregations,
        );

        if ([] === $aggregations) {
            return new CollectionAggregationResultDTO([], $table->groupBy);
        }

        return $this->aggregationProcessor->process(
            $table->collection,
            $query,
            $aggregations,
            $table->groupBy,
        );
    }
}
