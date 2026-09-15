<?php

declare(strict_types=1);

namespace App\Tabling\Service;

use App\Collectioning\DTO\CollectionFacetDTO;
use App\Collectioning\DTO\CollectionFacetResultDTO;
use App\Collectioning\DTO\CollectionQueryDTO;
use App\Collectioning\ServiceInterface\CollectionFacetProcessorInterface;
use App\Tabling\DTO\TableDefinitionDTO;

final readonly class TableFacetService
{
    public function __construct(private CollectionFacetProcessorInterface $facetProcessor)
    {
    }

    /** @return list<CollectionFacetResultDTO> */
    public function load(TableDefinitionDTO $table, CollectionQueryDTO $query): array
    {
        $facets = array_map(
            static fn ($facet): CollectionFacetDTO => new CollectionFacetDTO(
                $facet->field,
                $facet->limit,
                $facet->includeMissing,
                $facet->excludeOwnFilter,
            ),
            $table->facets,
        );

        if ([] === $facets) {
            return [];
        }

        return $this->facetProcessor->process($table->collection, $query, $facets);
    }
}
