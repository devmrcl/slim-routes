<?php

declare(strict_types=1);

namespace Mrcl\SlimRoutes\Attribute;

use Attribute;
use Mrcl\SlimRoutes\Routing\HttpMethod;

use function array_reverse;
use function is_string;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class Route
{
    public readonly array $method;
    public readonly array $middleware;
    public readonly array $version;

    /**
     * This attribute maps a route to an action.
     *
     * @param string $pattern [optional] <p>Pattern</p>
     * @param string|string[] $method [optional] HTTP methods, case-insensitive strings
     * @param string|string[] $middleware [optional] <p>Middleware</p>
     * @param string|string[] $version [optional] <p>API version, <b>overrides #[Controller(version)]</b></p>
     * @param ?string $groupId [optional] <p>Group ID, <b>overrides #[Controller(groupId)]</b></p>
     * @param ?int $priority [optional] <p>Route priority</p>
     * @param ?string $name [optional] <p>Name</p>
     */
    public function __construct(
        public readonly string $pattern = '',
        string|array $method = [HttpMethod::GET],
        string|array $middleware = [],
        string|array $version = [],
        public readonly ?string $groupId = null,
        public readonly ?int $priority = null,
        public readonly ?string $name = null
    ) {
        $this->method     = is_string($method) ? [$method] : $method;
        $this->middleware = array_reverse(is_string($middleware) ? [$middleware] : $middleware);
        $this->version    = is_string($version) ? [$version] : $version;
    }
}
