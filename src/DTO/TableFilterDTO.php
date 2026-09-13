<?php

declare(strict_types=1);

namespace App\Tabling\DTO;

final readonly class TableFilterDTO
{
    /** @param list<array{label:string,value:string|int|bool}> $options */
    public function __construct(
        public string $name,
        public string $label,
        public string $type = 'text',
        public ?string $placeholder = null,
        public array $options = [],
    ) {
    }
}
