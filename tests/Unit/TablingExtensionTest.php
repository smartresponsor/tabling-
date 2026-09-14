<?php

declare(strict_types=1);

namespace App\Tabling\Tests\Unit;

use App\Tabling\DependencyInjection\TablingExtension;
use App\Tabling\Service\TableActionMetadataBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class TablingExtensionTest extends TestCase
{
    public function testLoadsServicesAndExposesCanonicalAlias(): void
    {
        $container = new ContainerBuilder();
        $extension = new TablingExtension();

        self::assertSame('tabling', $extension->getAlias());

        $extension->load([], $container);

        self::assertTrue($container->hasDefinition(TableActionMetadataBuilder::class));
    }
}
