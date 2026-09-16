<?php

declare(strict_types=1);

namespace App\Tabling\Builder;

use App\Tabling\DTO\TableAggregationDTO;

final class TableAggregations
{
    /** @var list<TableAggregationDTO> */
    private array $aggregations = [];

    /** @var list<string> */
    private array $groupBy = [];

    public function count(string $name = 'count', ?string $label = null, ?string $field = null): self
    {
        return $this->add(new TableAggregationDTO($name, $label ?? $this->humanize($name), 'count', $field));
    }

    public function sum(string $name, string $field, ?string $label = null): self
    {
        return $this->add(new TableAggregationDTO($name, $label ?? $this->humanize($name), 'sum', $field));
    }

    public function avg(string $name, string $field, ?string $label = null): self
    {
        return $this->add(new TableAggregationDTO($name, $label ?? $this->humanize($name), 'avg', $field));
    }

    public function min(string $name, string $field, ?string $label = null): self
    {
        return $this->add(new TableAggregationDTO($name, $label ?? $this->humanize($name), 'min', $field));
    }

    public function max(string $name, string $field, ?string $label = null): self
    {
        return $this->add(new TableAggregationDTO($name, $label ?? $this->humanize($name), 'max', $field));
    }

    public function groupBy(string ...$fields): self
    {
        foreach ($fields as $field) {
            if (!in_array($field, $this->groupBy, true)) {
                $this->groupBy[] = $field;
            }
        }

        return $this;
    }

    public function add(TableAggregationDTO $aggregation): self
    {
        $this->aggregations[] = $aggregation;

        return $this;
    }

    /** @return list<TableAggregationDTO> */
    public function all(): array
    {
        return $this->aggregations;
    }

    /** @return list<string> */
    public function groups(): array
    {
        return $this->groupBy;
    }

    private function humanize(string $value): string
    {
        return ucfirst(trim((string) preg_replace('/(?<!^)[A-Z]/', ' $0', str_replace(['-', '_'], ' ', $value))));
    }
}
