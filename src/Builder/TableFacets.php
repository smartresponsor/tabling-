<?php

declare(strict_types=1);

namespace App\Tabling\Builder;

use App\Tabling\DTO\TableFacetDTO;

final class TableFacets
{
    /** @var list<TableFacetDTO> */
    private array $facets = [];

    public function add(TableFacetDTO $facet): self
    {
        $this->facets[] = $facet;

        return $this;
    }

    public function terms(
        string $field,
        ?string $label = null,
        int $limit = 20,
        bool $includeMissing = false,
        bool $excludeOwnFilter = true,
    ): self {
        return $this->add(new TableFacetDTO(
            $field,
            $label ?? $this->humanize($field),
            $limit,
            $includeMissing,
            $excludeOwnFilter,
        ));
    }

    /** @return list<TableFacetDTO> */
    public function all(): array
    {
        return $this->facets;
    }

    private function humanize(string $value): string
    {
        return ucfirst(trim((string) preg_replace('/(?<!^)[A-Z]/', ' $0', str_replace(['-', '_'], ' ', $value))));
    }
}
