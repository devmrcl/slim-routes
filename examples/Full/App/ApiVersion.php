<?php

declare(strict_types=1);

namespace Mrcl\Examples\SlimRoutes\Full\App;

use Mrcl\Examples\SlimRoutes\Full\App\Middleware\ApiVersionFirstMiddleware;
use Mrcl\Examples\SlimRoutes\Full\App\Middleware\ApiVersionSecondMiddleware;
use Mrcl\SlimRoutes\Routing\RoutePriority;
use Mrcl\SlimRoutes\Routing\VersionConfiguration;

final class ApiVersion
{
    public const NONE = VersionConfiguration::NONE;
    public const V1   = 'v1';
    public const V2   = 'v2';

    /**
     * @var VersionConfiguration[]
     */
    public readonly array $get;

    public function __construct()
    {
        $this->get = [
            self::NONE => new VersionConfiguration(self::NONE, priority: RoutePriority::HIGHEST, default: false),
            self::V1 => new VersionConfiguration(self::V1),
            self::V2 => new VersionConfiguration(self::V2, [ApiVersionFirstMiddleware::class, ApiVersionSecondMiddleware::class])
        ];
    }
}
