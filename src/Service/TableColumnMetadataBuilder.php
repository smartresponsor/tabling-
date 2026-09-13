<?php

declare(strict_types=1);

namespace App\Tabling\Service;

final readonly class TableColumnMetadataBuilder
{
    /**
     * @param list<array<string, mixed>> $rows
     *
     * @return list<array<string, mixed>>
     */
    public function build(array $rows, string $resourceLabel): array
    {
        $columns = [
            $this->column('title', $resourceLabel, searchable: true, sortable: true),
            $this->column('code', 'Code', isCode: true, searchable: true, sortable: true),
            $this->column('owner', 'Owner', filterable: true, sortable: true),
            $this->column('status', 'Status', isStatus: true, filterable: true, sortable: true),
            $this->column('locale', 'Locale', filterable: true, sortable: true),
        ];
        $known = array_column($columns, 'key');
        foreach (array_keys($rows[0] ?? []) as $key) {
            if ('id' !== $key && !in_array($key, $known, true)) {
                $columns[] = $this->column($key, $this->humanize($key), searchable: true, sortable: true);
            }
        }

        return $columns;
    }

    /** @return array<string, mixed> */
    private function column(string $key, string $label, bool $isCode = false, bool $isStatus = false, bool $searchable = false, bool $filterable = false, bool $sortable = false): array
    {
        return compact('key', 'label', 'isCode', 'isStatus', 'searchable', 'filterable', 'sortable') + ['type' => 'text'];
    }

    private function humanize(string $value): string
    {
        return ucfirst(trim((string) preg_replace('/(?<!^)[A-Z]/', ' $0', str_replace(['-', '_'], ' ', $value))));
    }
}
