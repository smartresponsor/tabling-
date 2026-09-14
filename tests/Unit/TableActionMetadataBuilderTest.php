<?php

declare(strict_types=1);

namespace App\Tabling\Tests\Unit;

use App\Tabling\DTO\TableActionDTO;
use App\Tabling\Service\TableActionMetadataBuilder;
use PHPUnit\Framework\TestCase;

final class TableActionMetadataBuilderTest extends TestCase
{
    public function testBuildsProviderNeutralActionMetadata(): void
    {
        $action = new TableActionDTO(
            'delete',
            'Delete',
            'crud_delete',
            ['id' => 42],
            'DELETE',
            'row',
            true,
        );

        $metadata = (new TableActionMetadataBuilder())->build($action, '/items/delete/42');

        self::assertSame('danger', $metadata['variant']);
        self::assertSame('delete', $metadata['operation']);
        self::assertSame('/items/delete/42', $metadata['href']);
        self::assertSame('DELETE', $metadata['permission']);
    }

    public function testBuildsPrimaryDisabledActionMetadata(): void
    {
        $action = new TableActionDTO(
            'new',
            'Create',
            'crud_new',
            [],
            null,
            'table',
            false,
            false,
        );

        $metadata = (new TableActionMetadataBuilder())->build($action);

        self::assertSame('primary', $metadata['variant']);
        self::assertSame('disabled', $metadata['visibility']);
        self::assertFalse($metadata['enabled']);
        self::assertNull($metadata['href']);
    }
}
