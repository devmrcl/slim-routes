<?php

declare(strict_types=1);

namespace Mrcl\SlimRoutes\Attribute;

use Attribute;

use function array_reverse;
use function is_string;

#[Attribute(Attribute::TARGET_CLASS)]
final class Controller
{
    public readonly array $middleware;
    public readonly array $version;

    /**
     * This attribute marks an action controller as routable.
     * Arguments apply to all routes defined within the class.
     *
     * @param string $pattern [optional] <p>The routes' pattern prefix</p>
     * @param string|string[] $middleware [optional] <p>The routes' middleware, use <b>FQCN</b></p>
     * @param string|string[] $version [optional] <p>The routes' API version, <b>can get overriden by #[Route]</b></p>
     * @param ?string $groupId [optional] <p>The routes' group ID, <b>can get overriden by #[Route]</b></p>
     */
    public function __construct(
        public readonly string $pattern = '',
        string|array $middleware = [],
        string|array $version = [],
        public readonly ?string $groupId = null,
    ) {
        $this->middleware = array_reverse(is_string($middleware) ? [$middleware] : $middleware);
        $this->version    = is_string($version) ? [$version] : $version;
    }
}
