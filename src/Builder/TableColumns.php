<?php

declare(strict_types=1);

namespace App\Tabling\Builder;

use App\Tabling\DTO\TableColumnDTO;

final class TableColumns
{
    /** @var list<TableColumnDTO> */
    private array $columns = [];

    public function add(TableColumnDTO $column): self
    {
        $this->columns[] = $column;

        return $this;
    }

    public function text(string $field, ?string $label = null, bool $sortable = false, bool $filterable = false, bool $searchable = false, bool $visible = true): self
    {
        return $this->add(new TableColumnDTO(
            $field,
            $label ?? $this->humanize($field),
            'text',
            $sortable,
            $filterable,
            $searchable,
            $visible,
        ));
    }

    public function status(string $field = 'status', ?string $label = null): self
    {
        return $this->add(new TableColumnDTO($field, $label ?? $this->humanize($field), 'status', true, true, false));
    }

    public function dateTime(string $field, ?string $label = null, bool $sortable = true): self
    {
        return $this->add(new TableColumnDTO($field, $label ?? $this->humanize($field), 'dateTime', $sortable));
    }

    /** @return list<TableColumnDTO> */
    public function all(): array
    {
        return $this->columns;
    }

    private function humanize(string $value): string
    {
        return ucfirst(trim((string) preg_replace('/(?<!^)[A-Z]/', ' $0', str_replace(['-', '_'], ' ', $value))));
    }
}
