<?php

declare(strict_types=1);

namespace App\Tabling\Service;

use App\Tabling\DTO\TableActionDTO;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final readonly class TableActionVisibilityResolver
{
    public function __construct(private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public function isVisible(TableActionDTO $action, mixed $subject = null): bool
    {
        if (!$action->enabled) {
            return false;
        }

        if (null === $action->permission || '' === $action->permission) {
            return true;
        }

        return $this->authorizationChecker->isGranted($action->permission, $subject);
    }
}
