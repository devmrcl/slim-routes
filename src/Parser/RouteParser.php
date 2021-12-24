<?php

declare(strict_types=1);

namespace Mrcl\SlimRoutes\Parser;

use Mrcl\SlimRoutes\Attribute\Controller;
use Mrcl\SlimRoutes\Attribute\Route;
use Mrcl\SlimRoutes\Exception\RouteSetupException;
use Mrcl\SlimRoutes\Routing\VersionConfiguration;
use Mrcl\SlimRoutes\Routing\HttpMethod;
use Mrcl\SlimRoutes\Routing\SlimRoute;
use Mrcl\SlimRoutes\Routing\GroupConfiguration;
use Mrcl\SlimRoutes\Util\RouteUtil;
use ReflectionClass;
use ReflectionException;

use function array_filter;
use function array_map;
use function array_search;
use function array_splice;
use function array_unique;
use function array_values;
use function class_exists;
use function count;
use function is_string;
use function sprintf;
use function strtoupper;

final class RouteParser
{
    /**
     * @param string[] $classes
     * @param GroupConfiguration[] $groups
     * @param string[] $anyMethods
     * @param VersionConfiguration[] $apiVersions
     * @param ?int $defaultRoutePriority
     */
    public function __construct(
        private array $classes,
        private array $groups,
        private array $anyMethods,
        private array $apiVersions,
        private ?int $defaultRoutePriority
    ) {
    }

    /**
     * @return SlimRoute[]
     */
    public function parse(): array
    {
        $routes = [];
        foreach ($this->classes as $class) {
            try {
                $reflectionClass = new ReflectionClass($class);
            } catch (ReflectionException) {
                continue;
            }

            $controllerAttribute = $reflectionClass->getAttributes(Controller::class)[0] ?? null;
            $routeAttribute      = $reflectionClass->getAttributes(Route::class)[0] ?? null;
            if ($reflectionClass->isAbstract() && ($controllerAttribute || $routeAttribute)) {
                throw new RouteSetupException(sprintf('[%s] Abstract classes cannot be routed!', $reflectionClass->name));
            }

            if ($controllerAttribute) {
                /**
                 * @var Controller $instance
                 */
                $instance         = $controllerAttribute->newInstance();
                $controllerParser = new ControllerAttributeParser($reflectionClass, $instance);
                foreach ($reflectionClass->getMethods() as $reflectionMethod) {
                    $routeAttribute = $reflectionMethod->getAttributes(Route::class)[0] ?? null;
                    if ($routeAttribute) {
                        if (!$reflectionMethod->isPublic()) {
                            throw new RouteSetupException(sprintf('[%s] Action method cannot be routed!', $reflectionClass->name . ':' . $reflectionMethod->name));
                        }
                        /**
                         * @var Route $instance
                         */
                        $instance    = $routeAttribute->newInstance();
                        $routeParser = new RouteAttributeParser($reflectionMethod, $instance, $controllerParser);
                        $routes      = [...$routes, ...$this->convert($routeParser)];
                    }
                }
            } elseif ($routeAttribute) {
                /**
                 * @var Route $instance
                 */
                $instance    = $routeAttribute->newInstance();
                $routeParser = new RouteAttributeParser($reflectionClass, $instance);
                $routes      = [...$routes, ...$this->convert($routeParser)];
            }
        }

        return $routes;
    }

    /**
     * @param RouteAttributeParser $parser
     * @return SlimRoute[]
     */
    private function convert(RouteAttributeParser $parser): array
    {
        $group      = $this->findRouteGroup($parser);
        $middleware = $parser->getMiddleware();
        [$groupPattern, $groupMiddleware] = $group?->flatten() ?: ['', []];
        $middleware = [...$middleware, ...$groupMiddleware];
        $this->checkMiddleware($middleware, $parser);
        $apiVersions = $this->findApiVersions($parser);

        $routes = [];
        foreach ($apiVersions as $version) {
            $pattern  = RouteUtil::harmonizePattern($version->version, $groupPattern, $parser->getPattern());
            $name     = RouteUtil::harmonizeName($version->version, $parser->getRouteName());
            $methods  = $this->harmonizeHttpMethods($parser);
            $priority = $parser->getPriority() ?? $version->priority ?? $this->defaultRoutePriority;
            $routes[] = new SlimRoute(
                $pattern,
                $methods,
                $parser->getCallable(),
                [...$middleware, ...$version->middleware],
                $name,
                $priority,
            );
        }
        return $routes;
    }

    private function findRouteGroup(RouteAttributeParser $parser): ?GroupConfiguration
    {
        $id = $parser->getGroupId();
        if ($id === null) {
            return null;
        }
        return array_values(array_filter($this->groups, fn($group) => $group->id === $id))[0]
            ?? throw new RouteSetupException(sprintf('[%s] Group with ID %s not found. Make sure the GroupConfiguration was added.', $parser->getCallable(), $id));
    }

    /**
     * @return VersionConfiguration[]
     */
    private function findApiVersions(RouteAttributeParser $parser): array
    {
        $versions = $parser->getApiVersion();
        if (count($versions) === 0) {
            return array_filter($this->apiVersions, fn($v) => $v->default);
        } else {
            $foundVersions = [];
            foreach ($versions as $version) {
                $foundVersions[] = array_values(array_filter($this->apiVersions, fn($v) => $v->version === $version))[0]
                    ?? throw new RouteSetupException(sprintf('[%s] API version \'%s\' not found. Make sure the VersionConfiguration was added.', $parser->getCallable(), $version));
            }
            return array_unique($foundVersions);
        }
    }

    /**
     * @param string[] $middleware
     */
    private function checkMiddleware(array $middleware, RouteAttributeParser $parser): void
    {
        foreach ($middleware as $mw) {
            if (!class_exists($mw)) {
                throw new RouteSetupException(sprintf('[%s] Middleware %s does not exist!', $parser->getCallable(), $mw));
            }
        }
    }

    /**
     * @return string[]
     */
    private function harmonizeHttpMethods(RouteAttributeParser $parser): array
    {
        $methods = array_map(
            fn($m) => is_string($m)
                ? strtoupper($m)
                : throw new RouteSetupException(sprintf('[%s] Methods must be of type string!', $parser->getCallable())),
            $parser->getMethods()
        );
        $methods = array_unique($methods);
        $any     = array_search(HttpMethod::ANY, $methods, true);
        if ($any !== false) {
            array_splice($methods, $any, 1, $this->anyMethods);
        }
        return array_unique($methods);
    }
}
