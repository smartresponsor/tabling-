<?php

declare(strict_types=1);

namespace App\Tabling\Service;

use App\Collectioning\DTO\CollectionQueryDTO;
use App\Tabling\DTO\TableDefinitionDTO;

final readonly class PrimeReactCollectionQueryMapper
{
    public function __construct(private TableCollectionQueryBuilder $builder)
    {
    }

    /** @param array<string, mixed> $payload */
    public function map(array $payload, TableDefinitionDTO $table): CollectionQueryDTO
    {
        $rows = is_numeric($payload['rows'] ?? null) ? max(1, (int) $payload['rows']) : $table->collection->defaultPageSize;
        $first = is_numeric($payload['first'] ?? null) ? max(0, (int) $payload['first']) : 0;
        $page = intdiv($first, $rows) + 1;
        $search = isset($payload['globalFilter']) && is_scalar($payload['globalFilter']) ? (string) $payload['globalFilter'] : null;

        $filters = [];
        foreach (($payload['filters'] ?? []) as $field => $definition) {
            if (!is_string($field)) {
                continue;
            }
            $value = is_array($definition) ? ($definition['value'] ?? null) : $definition;
            if (null !== $value && !is_array($value)) {
                $filters[] = ['field' => $field, 'value' => $value];
            }
        }

        $sorts = [];
        if (isset($payload['multiSortMeta']) && is_array($payload['multiSortMeta'])) {
            foreach ($payload['multiSortMeta'] as $sort) {
                if (!is_array($sort) || !is_string($sort['field'] ?? null) || !is_numeric($sort['order'] ?? null)) {
                    continue;
                }
                $sorts[] = ['field' => $sort['field'], 'direction' => ((int) $sort['order']) < 0 ? 'desc' : 'asc'];
            }
        } elseif (is_string($payload['sortField'] ?? null) && is_numeric($payload['sortOrder'] ?? null)) {
            $sorts[] = ['field' => $payload['sortField'], 'direction' => ((int) $payload['sortOrder']) < 0 ? 'desc' : 'asc'];
        }

        return $this->builder->build(
            $table,
            $page,
            $rows,
            $search,
            $filters,
            $sorts,
            $this->fields($payload),
        );
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return list<string>
     */
    private function fields(array $payload): array
    {
        return isset($payload['fields']) && is_array($payload['fields'])
            ? array_values(array_filter($payload['fields'], 'is_string'))
            : [];
    }
}
