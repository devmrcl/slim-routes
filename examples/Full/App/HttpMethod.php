<?php

declare(strict_types=1);

namespace Mrcl\Examples\SlimRoutes\Full\App;

use Mrcl\SlimRoutes\Routing\HttpMethod as SlimRoutesHttpMethods;

final class HttpMethod extends SlimRoutesHttpMethods
{
    public const LINK   = 'LINK';
    public const UNLINK = 'UNLINK';
}
