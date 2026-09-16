<?php

declare(strict_types=1);

namespace App\Tabling\DTO;

final readonly class TableAggregationDTO
{
    public function __construct(
        public string $name,
        public string $label,
        public string $function,
        public ?string $field = null,
    ) {
    }
}
