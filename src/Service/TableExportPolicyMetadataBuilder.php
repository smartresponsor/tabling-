<?php

declare(strict_types=1);

namespace App\Tabling\Service;

use App\Tabling\DTO\TableDefinitionDTO;
use App\Tabling\DTO\TableExportPolicyDTO;

final readonly class TableExportPolicyMetadataBuilder
{
    /** @return array<string, mixed>|null */
    public function metadata(TableDefinitionDTO $definition): ?array
    {
        if (!($definition->capabilities ?? new \App\Tabling\DTO\TableCapabilitiesDTO())->export) {
            return null;
        }

        $policy = $definition->exportPolicy ?? new TableExportPolicyDTO();

        return [
            'formats' => $policy->formats,
            'scopes' => $policy->scopes,
            'defaultScope' => $policy->defaultScope,
            'permission' => $policy->permission,
        ];
    }
}
