<?php

declare(strict_types=1);

namespace App\Tabling\Service;

use App\Tabling\DTO\TableActionDTO;

final readonly class TableActionMetadataBuilder
{
    /** @return array<string, mixed> */
    public function build(TableActionDTO $action, ?string $href = null): array
    {
        return [
            'label' => $action->label,
            'href' => $href,
            'routeName' => $action->routeName,
            'routeParameters' => $action->routeParameters,
            'variant' => $action->dangerous ? 'danger' : ('new' === $action->name ? 'primary' : 'default'),
            'operation' => $action->name,
            'scope' => $action->scope,
            'enabled' => $action->enabled,
            'visibility' => $action->enabled ? 'visible' : 'disabled',
            'permission' => $action->permission,
        ];
    }
}
