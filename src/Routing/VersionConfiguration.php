<?php

declare(strict_types=1);

namespace Mrcl\SlimRoutes\Routing;

use Mrcl\SlimRoutes\Exception\SlimRoutesException;

use Stringable;

use function array_reverse;
use function class_exists;
use function is_string;
use function sprintf;

final class VersionConfiguration implements Stringable
{
    public const NONE = '';

    public readonly array $middleware;

    /**
     * @param string $version API version, prefixes routes
     * @param string|string[] $middleware [optional] <p>Version middleware, use <b>FQCN</b></p>
     * @param ?int $priority [optional] <p>Version priority</p>
     * @param bool $default [optional] <p><b>true</b> Use version for all routes (that do not specify another version)</p><p><b>false</b> Use version only for routes that explicitly specify this version</p>
     */
    public function __construct(
        public readonly string $version,
        string|array $middleware = [],
        public readonly ?int $priority = null,
        public readonly bool $default = true
    ) {
        $this->middleware = array_reverse(is_string($middleware) ? [$middleware] : $middleware);
        foreach ($this->middleware as $mw) {
            if (!class_exists($mw)) {
                throw new SlimRoutesException(sprintf('Middleware %s of VersionConfiguration %s does not exists!', $mw, $this->version));
            }
        }
    }

    public function __toString(): string
    {
        return $this->version;
    }
}
