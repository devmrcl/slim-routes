<?php

declare(strict_types=1);

namespace Mrcl\SlimRoutes\Parser;

use Mrcl\SlimRoutes\Attribute\Controller;
use ReflectionClass;

final class ControllerAttributeParser
{
    public function __construct(
        private ReflectionClass $reflectionClass,
        private Controller $controller
    ) {
    }

    public function getCallable(): string
    {
        return $this->reflectionClass->name;
    }

    public function getPattern(): string
    {
        return $this->controller->pattern;
    }

    public function getGroupId(): ?string
    {
        return $this->controller->groupId;
    }

    public function getMiddleware(): array
    {
        return $this->controller->middleware;
    }

    public function getApiVersion(): array
    {
        return $this->controller->version;
    }
}
