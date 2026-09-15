<?php

declare(strict_types=1);

namespace App\Tabling\Service;

use App\Collectioning\DTO\CollectionQueryDTO;
use App\Tabling\DTO\TableDefinitionDTO;

final readonly class AntDesignCollectionQueryMapper
{
    public function __construct(private TableCollectionQueryBuilder $builder)
    {
    }

    /** @param array<string, mixed> $payload */
    public function map(array $payload, TableDefinitionDTO $table): CollectionQueryDTO
    {
        $page = is_numeric($payload['current'] ?? null) ? (int) $payload['current'] : 1;
        $pageSize = is_numeric($payload['pageSize'] ?? null) ? (int) $payload['pageSize'] : $table->collection->defaultPageSize;
        $search = $this->search($payload);

        $filters = [];
        foreach (($payload['filters'] ?? []) as $field => $value) {
            if (!is_string($field) || null === $value) {
                continue;
            }

            $filters[] = [
                'field' => $field,
                'operator' => is_array($value) ? 'in' : 'eq',
                'value' => $value,
            ];
        }

        $sorts = [];
        foreach (($payload['sorter'] ?? []) as $field => $direction) {
            if (!is_string($field) || !is_string($direction)) {
                continue;
            }
            $mapped = match ($direction) {
                'ascend', 'asc' => 'asc',
                'descend', 'desc' => 'desc',
                default => null,
            };
            if (null !== $mapped) {
                $sorts[] = ['field' => $field, 'direction' => $mapped];
            }
        }

        return $this->builder->build(
            $table,
            $page,
            $pageSize,
            $search,
            $filters,
            $sorts,
            $this->fields($payload),
        );
    }

    /** @param array<string, mixed> $payload */
    private function search(array $payload): ?string
    {
        foreach (['q', 'search', 'keyword'] as $key) {
            if (isset($payload[$key]) && is_scalar($payload[$key])) {
                return (string) $payload[$key];
            }
        }

        return null;
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
