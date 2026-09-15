<?php

declare(strict_types=1);

namespace App\Tabling\DTO;

use App\Collectioning\DTO\CollectionDefinitionDTO;

final readonly class TableDefinitionDTO
{
    /**
     * @param list<TableColumnDTO> $columns
     * @param list<TableActionDTO> $actions
     * @param array<string, mixed> $meta
     * @param list<TableFilterDTO> $filters
     * @param list<TableActionDTO> $bulkActions
     */
    public function __construct(
        public string $name,
        public CollectionDefinitionDTO $collection,
        public array $columns,
        public array $actions = [],
        public array $meta = [],
        public array $filters = [],
        public array $bulkActions = [],
    ) {
    }
}
