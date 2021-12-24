<?php

declare(strict_types=1);

namespace Mrcl\SlimRoutes\Routing;

class RoutePriority
{
    public const HIGHEST = -3;
    public const HIGHER  = -2;
    public const HIGH    = -1;
    public const NORMAL  = 0;
    public const LOW     = 1;
    public const LOWER   = 2;
    public const LOWEST  = 3;
}
