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
     */
    public function __construct(
        public string $name,
        public CollectionDefinitionDTO $collection,
        public array $columns,
        public array $actions = [],
        public array $meta = [],
    ) {
    }
}
