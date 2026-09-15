<?php

declare(strict_types=1);

namespace App\Tabling\Builder;

use App\Tabling\DTO\TableFilterDTO;

final class TableFilters
{
    /** @var list<TableFilterDTO> */
    private array $filters = [];

    public function add(TableFilterDTO $filter): self
    {
        $this->filters[] = $filter;

        return $this;
    }

    public function text(string $name, ?string $label = null, ?string $placeholder = null): self
    {
        return $this->add(new TableFilterDTO(
            $name,
            $label ?? $this->humanize($name),
            'text',
            $placeholder,
        ));
    }

    /** @param list<array{label:string,value:string|int|bool}> $options */
    public function select(string $name, array $options = [], ?string $label = null, ?string $placeholder = null): self
    {
        return $this->add(new TableFilterDTO(
            $name,
            $label ?? $this->humanize($name),
            'select',
            $placeholder,
            $options,
        ));
    }

    /** @return list<TableFilterDTO> */
    public function all(): array
    {
        return $this->filters;
    }

    private function humanize(string $value): string
    {
        return ucfirst(trim((string) preg_replace('/(?<!^)[A-Z]/', ' $0', str_replace(['-', '_'], ' ', $value))));
    }
}
