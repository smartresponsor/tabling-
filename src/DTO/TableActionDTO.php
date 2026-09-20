<?php

declare(strict_types=1);

namespace App\Tabling\DTO;

use App\Collectioning\DTO\CollectionDataScopeDTO;

final readonly class TableActionDTO
{
    /**
     * @param array<string, string|int> $routeParameters
     * @param list<string>              $allowedDataScopes
     */
    public function __construct(
        public string $name,
        public string $label,
        public string $routeName,
        public array $routeParameters = [],
        public ?string $permission = null,
        public string $scope = 'row',
        public bool $dangerous = false,
        public bool $enabled = true,
        public array $allowedDataScopes = [],
        public ?string $defaultDataScope = null,
    ) {
        if ('bulk' !== $scope) {
            if ([] !== $allowedDataScopes || null !== $defaultDataScope) {
                throw new \InvalidArgumentException('Data-scope policy is supported only for bulk table actions.');
            }

            return;
        }

        foreach ($allowedDataScopes as $dataScope) {
            if (!in_array($dataScope, [
                CollectionDataScopeDTO::CURRENT_PAGE,
                CollectionDataScopeDTO::FILTERED,
                CollectionDataScopeDTO::SELECTED,
            ], true)) {
                throw new \InvalidArgumentException(sprintf('Unsupported bulk action data scope "%s".', $dataScope));
            }
        }

        if (null !== $defaultDataScope && !in_array($defaultDataScope, $allowedDataScopes, true)) {
            throw new \InvalidArgumentException('Bulk action default data scope must be included in allowed data scopes.');
        }
    }
}
