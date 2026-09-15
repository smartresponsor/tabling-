<?php

declare(strict_types=1);

namespace App\Tabling\ServiceInterface;

use App\Tabling\DTO\TableViewDTO;

interface TableViewStoreInterface
{
    /** @return list<TableViewDTO> */
    public function all(string $tableName, string $ownerKey): array;

    public function find(string $tableName, string $ownerKey, string $viewKey): ?TableViewDTO;

    public function save(string $tableName, string $ownerKey, TableViewDTO $view): void;

    public function delete(string $tableName, string $ownerKey, string $viewKey): void;
}
