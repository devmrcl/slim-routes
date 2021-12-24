<?php

declare(strict_types=1);

namespace Mrcl\SlimRoutes\Routing;

use JsonSerializable;
use Stringable;

use function implode;
use function sprintf;

final class SlimRoute implements Stringable, JsonSerializable
{
    /**
     * @param string $pattern
     * @param string[] $methods
     * @param string $callable
     * @param string[] $middleware
     * @param ?string $name
     * @param ?int $priority
     */
    public function __construct(
        public readonly string $pattern,
        public readonly array $methods,
        public readonly string $callable,
        public readonly array $middleware = [],
        public readonly ?string $name = null,
        public readonly ?int $priority = null,
    ) {
    }

    public function __toString(): string
    {
        return sprintf(
            '[%s => %s %s]',
            $this->callable,
            implode('|', $this->methods),
            $this->pattern
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'methods' => $this->methods,
            'pattern' => $this->pattern,
            'name' => $this->name,
            'callable' => $this->callable,
            'middleware' => $this->middleware,
            'priority' => $this->priority
        ];
    }
}
