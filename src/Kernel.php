<?php

declare(strict_types=1);

namespace App\Tabling;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * Boots the standalone verification runtime while Tabling remains reusable as a bundle.
 */
final class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
