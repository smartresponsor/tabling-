<?php

declare(strict_types=1);

namespace App\Tabling\DTO;

final readonly class TableFacetDTO
{
    public function __construct(
        public string $field,
        public string $label,
        public int $limit = 20,
        public bool $includeMissing = false,
        public bool $excludeOwnFilter = true,
    ) {
    }
}
