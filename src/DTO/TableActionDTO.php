<?php

declare(strict_types=1);

namespace App\Tabling\DTO;

final readonly class TableActionDTO
{
    /** @param array<string, string|int> $routeParameters */
    public function __construct(
        public string $name,
        public string $label,
        public string $routeName,
        public array $routeParameters = [],
        public ?string $permission = null,
        public string $scope = 'row',
        public bool $dangerous = false,
        public bool $enabled = true,
    ) {
    }
}
