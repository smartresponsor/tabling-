<?php

declare(strict_types=1);

namespace App\Tabling\Tests\Unit;

use App\Collectioning\DTO\CollectionDataScopeDTO;
use App\Collectioning\DTO\CollectionDefinitionDTO;
use App\Collectioning\DTO\CollectionFieldPolicyDTO;
use App\Collectioning\DTO\CollectionPageDTO;
use App\Collectioning\DTO\CollectionQueryDTO;
use App\Collectioning\ServiceInterface\CollectionScopedReaderInterface;
use App\Tabling\DTO\TableCapabilitiesDTO;
use App\Tabling\DTO\TableDefinitionDTO;
use App\Tabling\DTO\TableExportPolicyDTO;
use App\Tabling\Service\TableDataScopeResolver;
use App\Tabling\Service\TableExportService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class TableExportServiceTest extends TestCase
{
    public function testSelectedExportDelegatesCanonicalIdentifierScopeAndPermission(): void
    {
        $reader = new class implements CollectionScopedReaderInterface {
            public ?CollectionDataScopeDTO $scope = null;

            public function read(CollectionDefinitionDTO $definition, CollectionQueryDTO $query, CollectionDataScopeDTO $scope): iterable
            {
                $this->scope = $scope;

                return [['id' => 10]];
            }
        };
        $authorization = $this->createMock(AuthorizationCheckerInterface::class);
        $authorization->expects(self::once())->method('isGranted')->with('EXPORT_USERS')->willReturn(true);
        $table = $this->table(new TableExportPolicyDTO(
            scopes: [CollectionDataScopeDTO::SELECTED],
            defaultScope: CollectionDataScopeDTO::SELECTED,
            permission: 'EXPORT_USERS',
        ));
        $query = new CollectionQueryDTO(new CollectionPageDTO(3, 25));

        $items = iterator_to_array((new TableExportService(
            $reader,
            new TableDataScopeResolver(),
            $authorization,
        ))->read($table, $query, selectedValues: [10, 20]));

        self::assertSame([['id' => 10]], $items);
        self::assertNotNull($reader->scope);
        self::assertSame(CollectionDataScopeDTO::SELECTED, $reader->scope->mode);
        self::assertCount(1, $reader->scope->selectionFilters);
        self::assertSame('id', $reader->scope->selectionFilters[0]->field);
        self::assertSame('in', $reader->scope->selectionFilters[0]->operator);
        self::assertSame([10, 20], $reader->scope->selectionFilters[0]->value);
    }

    public function testUsesDefaultFilteredScopeWithoutAuthorizationCheck(): void
    {
        $reader = new class implements CollectionScopedReaderInterface {
            public ?CollectionDataScopeDTO $scope = null;

            public function read(CollectionDefinitionDTO $definition, CollectionQueryDTO $query, CollectionDataScopeDTO $scope): iterable
            {
                $this->scope = $scope;

                return [];
            }
        };
        $authorization = $this->createMock(AuthorizationCheckerInterface::class);
        $authorization->expects(self::never())->method('isGranted');
        $table = $this->table(new TableExportPolicyDTO());

        iterator_to_array((new TableExportService(
            $reader,
            new TableDataScopeResolver(),
            $authorization,
        ))->read($table, new CollectionQueryDTO(new CollectionPageDTO())));

        self::assertNotNull($reader->scope);
        self::assertSame(CollectionDataScopeDTO::FILTERED, $reader->scope->mode);
    }

    public function testRejectsUnauthorizedAndDisabledExports(): void
    {
        $reader = $this->createMock(CollectionScopedReaderInterface::class);
        $reader->expects(self::never())->method('read');
        $authorization = $this->createMock(AuthorizationCheckerInterface::class);
        $authorization->expects(self::once())->method('isGranted')->with('EXPORT_USERS')->willReturn(false);
        $service = new TableExportService($reader, new TableDataScopeResolver(), $authorization);

        try {
            iterator_to_array($service->read(
                $this->table(new TableExportPolicyDTO(permission: 'EXPORT_USERS')),
                new CollectionQueryDTO(new CollectionPageDTO()),
            ));
            self::fail('Unauthorized export should fail.');
        } catch (AccessDeniedException) {
        }

        $disabled = new TableDefinitionDTO('disabled', $this->collection(), [], capabilities: new TableCapabilitiesDTO());
        $this->expectException(\LogicException::class);
        iterator_to_array($service->read($disabled, new CollectionQueryDTO(new CollectionPageDTO())));
    }

    public function testSelectedScopeRejectsCompositeIdentifiersAndDisallowedMembership(): void
    {
        $resolver = new TableDataScopeResolver();
        $composite = new TableDefinitionDTO(
            'composite',
            new CollectionDefinitionDTO(\stdClass::class, [], identifierFields: ['tenantId', 'id']),
            [],
        );

        try {
            $resolver->resolve($composite, CollectionDataScopeDTO::SELECTED, [1]);
            self::fail('Composite selected scope should fail.');
        } catch (\LogicException) {
        }

        $notFilterable = new TableDefinitionDTO(
            'not-filterable',
            new CollectionDefinitionDTO(\stdClass::class, [
                new CollectionFieldPolicyDTO('id', filterable: true, filterOperators: ['eq']),
            ], identifierFields: ['id']),
            [],
        );
        $this->expectException(\LogicException::class);
        $resolver->resolve($notFilterable, CollectionDataScopeDTO::SELECTED, [1]);
    }

    public function testExportPolicyRejectsInvalidDefaultScope(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new TableExportPolicyDTO(scopes: [CollectionDataScopeDTO::CURRENT_PAGE], defaultScope: CollectionDataScopeDTO::FILTERED);
    }

    private function table(TableExportPolicyDTO $policy): TableDefinitionDTO
    {
        return new TableDefinitionDTO(
            'users',
            $this->collection(),
            [],
            capabilities: new TableCapabilitiesDTO(rowSelection: true, export: true),
            exportPolicy: $policy,
        );
    }

    private function collection(): CollectionDefinitionDTO
    {
        return new CollectionDefinitionDTO(\stdClass::class, [
            new CollectionFieldPolicyDTO('id', filterable: true, sortable: true, filterOperators: ['eq', 'in']),
        ], identifierFields: ['id']);
    }
}
