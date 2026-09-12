<?php

declare(strict_types=1);

namespace App\Tabling\ServiceInterface;

use App\Tabling\DTO\TableDefinitionDTO;

interface TableProviderMapperInterface
{
    /** @return array<string, mixed> */
    public function map(TableDefinitionDTO $definition): array;

    public function provider(): string;
}
