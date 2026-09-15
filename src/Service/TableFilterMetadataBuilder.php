<?php

declare(strict_types=1);

namespace App\Tabling\Service;

use App\Tabling\DTO\TableFilterDTO;

final readonly class TableFilterMetadataBuilder
{
    /** @return list<array<string, mixed>> */
    public function build(string $resourceLabel): array
    {
        return array_map(
            fn (TableFilterDTO $filter): array => $this->metadata($filter),
            [
                new TableFilterDTO('q', 'Search', 'text', 'Search '.$resourceLabel),
                new TableFilterDTO('status', 'Status', 'select', 'Any status'),
            ],
        );
    }

    /** @return array<string, mixed> */
    public function metadata(TableFilterDTO $filter): array
    {
        return [
            'nameEntity' => $filter->name,
            'label' => $filter->label,
            'type' => $filter->type,
            'value' => null,
            'placeholder' => $filter->placeholder,
            'options' => $filter->options,
        ];
    }
}
