<?php

declare(strict_types=1);

namespace App\Tabling\Tests\Unit;

use App\Tabling\DTO\TableActionDTO;
use App\Tabling\Service\TableActionVisibilityResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class TableActionVisibilityResolverTest extends TestCase
{
    public function testResolvesDisabledOpenAndProtectedActions(): void
    {
        $subject = new \stdClass();
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker
            ->expects(self::once())
            ->method('isGranted')
            ->with('EDIT', $subject)
            ->willReturn(true);

        $resolver = new TableActionVisibilityResolver($authorizationChecker);

        self::assertFalse($resolver->isVisible(new TableActionDTO(
            'edit',
            'Edit',
            'crud_edit',
            enabled: false,
        ), $subject));

        self::assertTrue($resolver->isVisible(new TableActionDTO(
            'show',
            'Show',
            'crud_show',
        ), $subject));

        self::assertTrue($resolver->isVisible(new TableActionDTO(
            'edit',
            'Edit',
            'crud_edit',
            permission: 'EDIT',
        ), $subject));
    }
}
