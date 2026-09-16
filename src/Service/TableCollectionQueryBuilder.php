<?php

declare(strict_types=1);

namespace App\Tabling\Service;

use App\Collectioning\DTO\CollectionFilterDTO;
use App\Collectioning\DTO\CollectionPageDTO;
use App\Collectioning\DTO\CollectionQueryDTO;
use App\Collectioning\DTO\CollectionSortDTO;
use App\Tabling\DTO\TableDefinitionDTO;

final readonly class TableCollectionQueryBuilder
{
    /**
     * @param list<mixed>                               $filters
     * @param list<mixed>                               $sorts
     * @param list<mixed>                               $fields
     * @param array<string, int|float|string|bool>|null $cursor
     */
    public function build(
        TableDefinitionDTO $table,
        int $page,
        int $pageSize,
        ?string $search = null,
        array $filters = [],
        array $sorts = [],
        array $fields = [],
        ?array $cursor = null,
    ): CollectionQueryDTO {
        $definition = $table->collection;
        $policies = [];
        foreach ($definition->fields as $policy) {
            $policies[$policy->field] = $policy;
        }

        $queryFilters = [];
        foreach ($filters as $filter) {
            if (!is_array($filter) || !isset($filter['field'], $filter['value']) || !is_string($filter['field'])) {
                continue;
            }
            $field = $filter['field'];
            $operator = isset($filter['operator']) && is_string($filter['operator']) ? $filter['operator'] : 'eq';
            if (!isset($policies[$field])) {
                throw new \InvalidArgumentException(sprintf('Unknown collection filter field "%s".', $field));
            }
            if (!$policies[$field]->filterable) {
                throw new \InvalidArgumentException(sprintf('Collection field "%s" is not filterable.', $field));
            }
            if (!in_array($operator, $policies[$field]->filterOperators, true)) {
                throw new \InvalidArgumentException(sprintf('Collection field "%s" does not allow filter operator "%s".', $field, $operator));
            }
            $queryFilters[] = new CollectionFilterDTO($field, $operator, $filter['value']);
        }

        $querySorts = [];
        $effectiveSorts = [] !== $sorts ? $sorts : $this->defaultSorts($table);
        foreach ($effectiveSorts as $sort) {
            if (!is_array($sort) || !isset($sort['field'], $sort['direction']) || !is_string($sort['field']) || !is_string($sort['direction'])) {
                continue;
            }
            $field = $sort['field'];
            $direction = strtolower($sort['direction']);
            if (!isset($policies[$field])) {
                throw new \InvalidArgumentException(sprintf('Unknown collection sort field "%s".', $field));
            }
            if (!$policies[$field]->sortable) {
                throw new \InvalidArgumentException(sprintf('Collection field "%s" is not sortable.', $field));
            }
            if (!in_array($direction, ['asc', 'desc'], true)) {
                throw new \InvalidArgumentException(sprintf('Unsupported collection sort direction "%s" for field "%s".', $direction, $field));
            }
            $querySorts[] = new CollectionSortDTO($field, $direction);
        }

        $queryFields = [];
        foreach ($fields as $field) {
            if (!is_string($field)) {
                continue;
            }
            if (!isset($policies[$field]) || !$policies[$field]->projectable) {
                throw new \InvalidArgumentException(sprintf('Collection field "%s" is not projectable.', $field));
            }
            $queryFields[] = $field;
        }

        $normalizedSearch = null === $search ? null : trim($search);

        return new CollectionQueryDTO(
            new CollectionPageDTO(max(1, $page), max(1, min($definition->maxPageSize, $pageSize))),
            '' === $normalizedSearch ? null : $normalizedSearch,
            $queryFilters,
            $querySorts,
            $queryFields,
            $cursor,
        );
    }

    /** @return list<array{field:string,direction:string}> */
    private function defaultSorts(TableDefinitionDTO $table): array
    {
        return array_map(
            static fn (CollectionSortDTO $sort): array => ['field' => $sort->field, 'direction' => $sort->direction],
            $table->defaultSorts,
        );
    }
}
