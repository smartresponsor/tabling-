<?php

declare(strict_types=1);

namespace App\Tabling\ServiceInterface;

use App\Tabling\DTO\TableDefinitionDTO;

interface TableDefinitionProviderInterface
{
    public function definition(): TableDefinitionDTO;
}
