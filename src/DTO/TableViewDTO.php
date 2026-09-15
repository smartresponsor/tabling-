<?php

declare(strict_types=1);

namespace App\Tabling\DTO;

use App\Collectioning\DTO\CollectionFilterDTO;
use App\Collectioning\DTO\CollectionSortDTO;

final readonly class TableViewDTO
{
    /**
     * @param list<TableColumnStateDTO> $columns
     * @param list<CollectionFilterDTO> $filters
     * @param list<CollectionSortDTO>   $sorts
     * @param array<string, mixed>      $meta
     */
    public function __construct(
        public string $key,
        public string $label,
        public array $columns = [],
        public ?string $search = null,
        public array $filters = [],
        public array $sorts = [],
        public ?int $pageSize = null,
        public bool $default = false,
        public array $meta = [],
    ) {
    }
}
