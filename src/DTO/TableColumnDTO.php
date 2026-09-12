<?php

declare(strict_types=1);

namespace App\Tabling\DTO;

final readonly class TableColumnDTO
{
    public function __construct(
        public string $field,
        public string $label,
        public string $type = 'text',
        public bool $sortable = false,
        public bool $filterable = false,
        public bool $searchable = false,
        public bool $visible = true,
    ) {
    }
}
