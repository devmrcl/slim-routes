<?php

declare(strict_types=1);

namespace Mrcl\SlimRoutes\Routing;

use Mrcl\SlimRoutes\Exception\SlimRoutesException;

use Mrcl\SlimRoutes\Util\RouteUtil;

use function array_reverse;
use function class_exists;
use function is_string;
use function sprintf;

final class GroupConfiguration
{
    private readonly array $middleware;

    /**
     * @param string $id Group ID
     * @param string $pattern Group pattern
     * @param string|string[] $middleware Group middleware, use <b>FQCN</b>
     * @param ?GroupConfiguration $parent Parent group
     */
    public function __construct(
        public readonly string $id,
        private readonly string $pattern,
        string|array $middleware = [],
        private readonly ?GroupConfiguration $parent = null
    ) {
        $this->middleware = array_reverse(is_string($middleware) ? [$middleware] : $middleware);
        foreach ($this->middleware as $mw) {
            if (!class_exists($mw)) {
                throw new SlimRoutesException(sprintf('Middleware %s of GroupConfiguration %s does not exists!', $mw, $this->id));
            }
        }
    }

    /**
     * @return array[pattern: string, middleware: string[]]
     */
    public function flatten(): array
    {
        if (!$this->parent) {
            return [
                RouteUtil::harmonizePattern($this->pattern),
                $this->middleware
            ];
        } else {
            [$pattern, $middleware] = $this->parent->flatten();
            return [
                $pattern . RouteUtil::harmonizePattern($this->pattern),
                [...$this->middleware, ...$middleware]
            ];
        }
    }
}
