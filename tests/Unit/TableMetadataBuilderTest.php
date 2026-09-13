<?php

declare(strict_types=1);

namespace App\Tabling\Tests\Unit;

use App\Tabling\Service\TableColumnMetadataBuilder;
use App\Tabling\Service\TableFilterMetadataBuilder;
use PHPUnit\Framework\TestCase;

final class TableMetadataBuilderTest extends TestCase
{
    public function testBuildsProviderNeutralColumnsFromRows(): void
    {
        $columns = (new TableColumnMetadataBuilder())->build([
            ['id' => 1, 'title' => 'A', 'status' => 'active', 'displayName' => 'Alpha'],
        ], 'Users');

        self::assertSame('title', $columns[0]['key']);
        self::assertSame('Users', $columns[0]['label']);
        self::assertTrue($columns[0]['sortable']);
        self::assertSame('displayName', $columns[5]['key']);
        self::assertSame('Display Name', $columns[5]['label']);
    }

    public function testBuildsCanonicalFilters(): void
    {
        $filters = (new TableFilterMetadataBuilder())->build('Users');

        self::assertSame('q', $filters[0]['nameEntity']);
        self::assertSame('Search Users', $filters[0]['placeholder']);
        self::assertSame('status', $filters[1]['nameEntity']);
        self::assertSame('select', $filters[1]['type']);
    }
}
