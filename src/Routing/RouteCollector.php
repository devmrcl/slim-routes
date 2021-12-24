<?php

declare(strict_types=1);

namespace Mrcl\SlimRoutes\Routing;

use Mrcl\SlimRoutes\Cache\FileCache;
use Mrcl\SlimRoutes\Exception\RouteSetupException;
use Mrcl\SlimRoutes\Exception\SlimRoutesException;
use Mrcl\SlimRoutes\Parser\RouteParser;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

use function array_intersect;
use function count;
use function is_array;
use function sprintf;
use function usort;

final class RouteCollector
{
    /**
     * @var SlimRoute[]
     */
    private array       $routes     = [];
    private ?string     $cacheFile  = null;
    private bool        $prioritize = false;
    private RouteParser $parser;


    public function __construct(
        private RouteCollectorInterface|RouteCollectorProxyInterface $app
    ) {
    }

    public function collect(bool $fromCache = false): void
    {
        if ($fromCache) {
            $this->routes = $this->loadFromCache();
        } else {
            $routes = $this->parser->parse();
            $this->routeCheck($routes);
            if ($this->prioritize) {
                usort($routes, fn($ra, $rb) => $ra->priority <=> $rb->priority);
            }
            $this->routes = $routes;
            if ($this->cacheFile) {
                $this->cacheRoutes();
            }
        }
        $this->addToSlim();
    }

    public function setParser(RouteParser $parser): self
    {
        $this->parser = $parser;
        return $this;
    }

    public function prioritizeRoutes(bool $prioritize): self
    {
        $this->prioritize = $prioritize;
        return $this;
    }

    public function setCacheFile(?string $cacheFile): self
    {
        $this->cacheFile = $cacheFile;
        return $this;
    }

    /**
     * @return SlimRoute[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @return SlimRoute[]
     */
    private function loadFromCache(): array
    {
        $cached = (new FileCache($this->cacheFile))->read();
        if (!is_array($cached) || !($cached[0] ?? null) instanceof SlimRoute) {
            throw new SlimRoutesException(sprintf('Invalid cache file %s', $this->cacheFile));
        }
        return $cached;
    }

    private function cacheRoutes(): void
    {
        $cached = (new FileCache($this->cacheFile))->write($this->routes);
        if (!$cached) {
            throw new SlimRoutesException(sprintf('Caching to file %s was not successful', $this->cacheFile));
        }
    }

    /**
     * @param SlimRoute[] $routes
     */
    private function routeCheck(array $routes): void
    {
        $list = [];
        foreach ($routes as $route) {
            if ($listEntry = ($list[$route->pattern] ?? false)) {
                $duplicated = array_intersect($route->methods, $listEntry->methods);
                if (count($duplicated) > 0) {
                    throw new RouteSetupException(sprintf('Route duplicate found! %s (partially) overrides %s', $route, $listEntry));
                }
            } else {
                $list[$route->pattern] = $route;
            }
        }
    }

    private function addToSlim(): void
    {
        foreach ($this->routes as $route) {
            $slimNativeRoute = $this->app->map(
                $route->methods,
                $route->pattern,
                $route->callable
            );
            foreach ($route->middleware as $middleware) {
                $slimNativeRoute->add($middleware);
            }
            if ($route->name) {
                $slimNativeRoute->setName($route->name);
            }
        }
    }
}
