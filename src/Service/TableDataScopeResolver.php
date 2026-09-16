<?php

declare(strict_types=1);

namespace App\Tabling\Service;

use App\Collectioning\DTO\CollectionDataScopeDTO;
use App\Collectioning\DTO\CollectionFilterDTO;
use App\Tabling\DTO\TableDefinitionDTO;

final readonly class TableDataScopeResolver
{
    /** @param array<array-key, mixed> $selectedValues */
    public function resolve(TableDefinitionDTO $table, string $mode, array $selectedValues = []): CollectionDataScopeDTO
    {
        if (CollectionDataScopeDTO::SELECTED !== $mode) {
            return new CollectionDataScopeDTO($mode);
        }

        if (1 !== count($table->collection->identifierFields)) {
            throw new \LogicException('Selected table scope requires exactly one scalar collection identifier field.');
        }
        if ([] === $selectedValues
            || !array_is_list($selectedValues)
            || count(array_filter($selectedValues, 'is_scalar')) !== count($selectedValues)) {
            throw new \InvalidArgumentException('Selected table scope requires a non-empty scalar identifier list.');
        }

        $identifier = $table->collection->identifierFields[0];
        $policy = null;
        foreach ($table->collection->fields as $fieldPolicy) {
            if ($fieldPolicy->field === $identifier) {
                $policy = $fieldPolicy;
                break;
            }
        }
        if (null === $policy || !$policy->filterable || !in_array('in', $policy->filterOperators, true)) {
            throw new \LogicException(sprintf('Collection identifier "%s" does not allow selected-scope membership filtering.', $identifier));
        }

        return new CollectionDataScopeDTO(CollectionDataScopeDTO::SELECTED, [
            new CollectionFilterDTO($identifier, 'in', $selectedValues),
        ]);
    }
}
