<?php

declare(strict_types=1);

namespace App\Tabling\DTO;

use App\Collectioning\DTO\CollectionDefinitionDTO;
use App\Collectioning\DTO\CollectionSortDTO;

final readonly class TableDefinitionDTO
{
    /**
     * @param list<TableColumnDTO>    $columns
     * @param list<TableActionDTO>    $actions
     * @param array<string, mixed>    $meta
     * @param list<TableFilterDTO>    $filters
     * @param list<TableActionDTO>    $bulkActions
     * @param list<CollectionSortDTO> $defaultSorts
     * @param list<TableFacetDTO>     $facets
     */
    public function __construct(
        public string $name,
        public CollectionDefinitionDTO $collection,
        public array $columns,
        public array $actions = [],
        public array $meta = [],
        public array $filters = [],
        public array $bulkActions = [],
        public array $defaultSorts = [],
        public ?TableCapabilitiesDTO $capabilities = null,
        public array $facets = [],
    ) {
    }
}
