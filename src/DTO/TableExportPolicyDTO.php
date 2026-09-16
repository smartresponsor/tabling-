<?php

declare(strict_types=1);

namespace App\Tabling\DTO;

use App\Collectioning\DTO\CollectionDataScopeDTO;

final readonly class TableExportPolicyDTO
{
    /**
     * @param list<string> $formats
     * @param list<string> $scopes
     */
    public function __construct(
        public array $formats = ['csv'],
        public array $scopes = [CollectionDataScopeDTO::FILTERED, CollectionDataScopeDTO::CURRENT_PAGE],
        public string $defaultScope = CollectionDataScopeDTO::FILTERED,
        public ?string $permission = null,
    ) {
        if ([] === $formats) {
            throw new \InvalidArgumentException('Table export policy requires at least one format.');
        }
        if ([] === $scopes || !in_array($defaultScope, $scopes, true)) {
            throw new \InvalidArgumentException('Table export default scope must be included in allowed scopes.');
        }
        foreach ($scopes as $scope) {
            if (!in_array($scope, [
                CollectionDataScopeDTO::CURRENT_PAGE,
                CollectionDataScopeDTO::FILTERED,
                CollectionDataScopeDTO::SELECTED,
            ], true)) {
                throw new \InvalidArgumentException(sprintf('Unsupported table export scope "%s".', $scope));
            }
        }
    }
}
