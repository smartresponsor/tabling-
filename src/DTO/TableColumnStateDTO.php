<?php

declare(strict_types=1);

namespace App\Tabling\DTO;

final readonly class TableColumnStateDTO
{
    public function __construct(
        public string $field,
        public bool $visible = true,
        public ?int $order = null,
        public ?int $width = null,
        public ?string $pinned = null,
    ) {
    }
}
