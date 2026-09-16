<?php

declare(strict_types=1);

namespace App\Tabling\Service;

use App\Collectioning\DTO\CollectionQueryDTO;
use App\Collectioning\ServiceInterface\CollectionScopedReaderInterface;
use App\Tabling\DTO\TableDefinitionDTO;
use App\Tabling\DTO\TableExportPolicyDTO;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class TableExportService
{
    public function __construct(
        private CollectionScopedReaderInterface $scopedReader,
        private TableDataScopeResolver $scopeResolver,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    /**
     * @param list<int|float|string|bool> $selectedValues
     *
     * @return iterable<mixed>
     */
    public function read(
        TableDefinitionDTO $table,
        CollectionQueryDTO $query,
        ?string $scope = null,
        array $selectedValues = [],
    ): iterable {
        if (!($table->capabilities ?? new \App\Tabling\DTO\TableCapabilitiesDTO())->export) {
            throw new \LogicException(sprintf('Table "%s" does not enable export.', $table->name));
        }

        $policy = $table->exportPolicy ?? new TableExportPolicyDTO();
        if (null !== $policy->permission && '' !== $policy->permission
            && !$this->authorizationChecker->isGranted($policy->permission)) {
            throw new AccessDeniedException(sprintf('Export is not authorized for table "%s".', $table->name));
        }

        $effectiveScope = $scope ?? $policy->defaultScope;
        if (!in_array($effectiveScope, $policy->scopes, true)) {
            throw new \InvalidArgumentException(sprintf('Export scope "%s" is not allowed for table "%s".', $effectiveScope, $table->name));
        }

        return $this->scopedReader->read(
            $table->collection,
            $query,
            $this->scopeResolver->resolve($table, $effectiveScope, $selectedValues),
        );
    }
}
