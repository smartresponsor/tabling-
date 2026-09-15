<?php

declare(strict_types=1);

namespace App\Tabling\Builder;

use App\Tabling\DTO\TableActionDTO;

final class TableActions
{
    /** @var list<TableActionDTO> */
    private array $actions = [];

    /** @param array<string, string|int> $routeParameters */
    public function add(
        string $name,
        string $label,
        string $routeName,
        array $routeParameters = [],
        ?string $permission = null,
        string $scope = 'row',
        bool $dangerous = false,
        bool $enabled = true,
    ): self {
        $this->actions[] = new TableActionDTO(
            $name,
            $label,
            $routeName,
            $routeParameters,
            $permission,
            $scope,
            $dangerous,
            $enabled,
        );

        return $this;
    }

    /** @param array<string, string|int> $routeParameters */
    public function edit(string $routeName, array $routeParameters = [], ?string $permission = 'EDIT'): self
    {
        return $this->add('edit', 'Edit', $routeName, $routeParameters, $permission);
    }

    /** @param array<string, string|int> $routeParameters */
    public function delete(string $routeName, array $routeParameters = [], ?string $permission = 'DELETE'): self
    {
        return $this->add('delete', 'Delete', $routeName, $routeParameters, $permission, 'row', true);
    }

    /** @param array<string, string|int> $routeParameters */
    public function bulk(string $name, string $label, string $routeName, array $routeParameters = [], ?string $permission = null, bool $dangerous = false): self
    {
        return $this->add($name, $label, $routeName, $routeParameters, $permission, 'bulk', $dangerous);
    }

    /** @return list<TableActionDTO> */
    public function row(): array
    {
        return array_values(array_filter($this->actions, static fn (TableActionDTO $action): bool => 'bulk' !== $action->scope));
    }

    /** @return list<TableActionDTO> */
    public function bulkActions(): array
    {
        return array_values(array_filter($this->actions, static fn (TableActionDTO $action): bool => 'bulk' === $action->scope));
    }
}
