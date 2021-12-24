<?php

declare(strict_types=1);

namespace Mrcl\SlimRoutes\Parser;

use Mrcl\SlimRoutes\Attribute\Route;
use Mrcl\SlimRoutes\Util\RouteUtil;
use ReflectionClass;
use ReflectionMethod;

final class RouteAttributeParser
{
    public function __construct(
        private ReflectionClass|ReflectionMethod $reflection,
        private Route $route,
        private ?ControllerAttributeParser $controller = null
    ) {
    }

    public function getCallable(): string
    {
        if ($this->reflection instanceof ReflectionClass) {
            return $this->reflection->name;
        }
        return $this->controller?->getCallable() . ':' . $this->reflection->name;
    }

    public function getPattern(): string
    {
        return RouteUtil::harmonizePattern($this->controller?->getPattern(), $this->route->pattern);
    }

    public function getGroupId(): ?string
    {
        return $this->route->groupId ?: $this->controller?->getGroupId();
    }

    public function getMiddleware(): array
    {
        return [...$this->route->middleware, ...$this->controller?->getMiddleware() ?? []];
    }

    public function getApiVersion(): array
    {
        return $this->route->version ?: $this->controller?->getApiVersion() ?? [];
    }

    /**
     * @return string[]
     */
    public function getMethods(): array
    {
        return $this->route->method;
    }

    public function getPriority(): ?int
    {
        return $this->route->priority;
    }

    public function getRouteName(): ?string
    {
        return $this->route->name;
    }
}
