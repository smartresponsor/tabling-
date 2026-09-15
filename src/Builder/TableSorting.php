<?php

declare(strict_types=1);

namespace App\Tabling\Builder;

use App\Collectioning\DTO\CollectionSortDTO;

final class TableSorting
{
    /** @var list<CollectionSortDTO> */
    private array $sorts = [];

    public function asc(string $field): self
    {
        $this->sorts[] = new CollectionSortDTO($field, 'asc');

        return $this;
    }

    public function desc(string $field): self
    {
        $this->sorts[] = new CollectionSortDTO($field, 'desc');

        return $this;
    }

    /** @return list<CollectionSortDTO> */
    public function all(): array
    {
        return $this->sorts;
    }
}
