<?php

declare(strict_types=1);

namespace Mrcl\SlimRoutes;

use Mrcl\SlimRoutes\Exception\SlimRoutesException;
use Mrcl\SlimRoutes\Parser\RouteParser;
use Mrcl\SlimRoutes\Routing\RoutePriority;
use Mrcl\SlimRoutes\Routing\SlimRoute;
use Mrcl\SlimRoutes\Routing\RouteCollector;
use Mrcl\SlimRoutes\Routing\VersionConfiguration;
use Mrcl\SlimRoutes\Routing\GroupConfiguration;
use Mrcl\SlimRoutes\Util\ClassFinder;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;

use function array_filter;
use function array_reverse;
use function count;
use function dirname;
use function file_exists;
use function is_dir;
use function is_readable;
use function is_writable;
use function sprintf;

final class SlimRoutes
{
    /**
     * @var string[]
     */
    private array $directories = [];
    /**
     * @var GroupConfiguration[]
     */
    private array $groups = [];
    /**
     * @var string[]
     */
    private array $fileNamePattern = ['.+', 'php'];
    /**
     * @var VersionConfiguration[]
     */
    private array $apiVersions = [];
    /**
     * @var string[]
     */
    private array $anyMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    private RouteCollector $routeCollector;
    private bool           $hasCacheFile         = false;
    private ?int           $defaultRoutePriority = null;


    /**
     * @param RouteCollectorInterface|RouteCollectorProxyInterface $app
     * @param string ...$directories
     */
    public function __construct(
        RouteCollectorInterface|RouteCollectorProxyInterface $app,
        string ...$directories
    ) {
        if (count($directories) === 0) {
            throw new SlimRoutesException('No directory given');
        }
        foreach ($directories as $directory) {
            $this->addDirectory($directory);
        }

        $this->routeCollector = new RouteCollector($app);
    }

    /**
     * @param string $directory
     * @return $this
     */
    public function addDirectory(string $directory): self
    {
        if (!is_dir($directory)) {
            throw new SlimRoutesException(sprintf("%s is not a directory", $directory));
        }

        $this->directories[] = $directory;
        return $this;
    }

    /**
     * @param string $cacheFile
     * @return $this
     */
    public function enableCache(string $cacheFile): self
    {
        $this->hasCacheFile = file_exists($cacheFile);

        if ($this->hasCacheFile && !is_readable($cacheFile)) {
            throw new SlimRoutesException(sprintf('Cache file %s is not readable', $cacheFile));
        } elseif (!$this->hasCacheFile && !is_writable(($cacheDir = dirname($cacheFile)))) {
            throw new SlimRoutesException(sprintf('Cache file directory %s is not writable', $cacheDir));
        }

        $this->routeCollector->setCacheFile($cacheFile);
        return $this;
    }

    /**
     * @param GroupConfiguration $group
     * @return $this
     */
    public function addGroup(GroupConfiguration $group): self
    {
        $isUnique = count(array_filter($this->groups, fn($g) => $g->id === $group->id)) === 0;
        if (!$isUnique) {
            throw new SlimRoutesException(sprintf('Group ID must be unique. Given ID: %s', $group->id));
        }
        $this->groups[] = $group;
        return $this;
    }

    /**
     * @param VersionConfiguration $version
     * @return $this
     */
    public function addApiVersion(VersionConfiguration $version): self
    {
        $isUnique = count(array_filter($this->apiVersions, fn($v) => $v->version === $version->version)) === 0;
        if (!$isUnique) {
            throw new SlimRoutesException(sprintf('API Version must be unique. API Version: %s', $version->version));
        }
        $this->apiVersions[] = $version;
        return $this;
    }

    /**
     * @param string $fileNamePattern
     * @param string $fileExtensionPattern
     * @return $this
     */
    public function setFileNamePattern(string $fileNamePattern, string $fileExtensionPattern = 'php'): self
    {
        @preg_match($this->getFilePattern($fileNamePattern, $fileExtensionPattern), '');
        if (preg_last_error() !== PREG_NO_ERROR) {
            throw new SlimRoutesException('Invalid file name/extension pattern');
        }
        $this->fileNamePattern = [$fileNamePattern, $fileExtensionPattern];
        return $this;
    }

    /**
     * @param string $fileExtensionPattern
     * @return $this
     */
    public function useActionFilePattern(string $fileExtensionPattern = 'php'): self
    {
        return $this->setFileNamePattern('.+Action', $fileExtensionPattern);
    }

    /**
     * @param string $fileExtensionPattern
     * @return $this
     */
    public function useControllerFilePattern(string $fileExtensionPattern = 'php'): self
    {
        return $this->setFileNamePattern('.+Controller', $fileExtensionPattern);
    }

    /**
     * @param int $defaultPriority
     * @return $this
     */
    public function enableRoutePrioritization(int $defaultPriority = RoutePriority::NORMAL): self
    {
        $this->defaultRoutePriority = $defaultPriority;
        return $this;
    }

    /**
     * @param string[] $methods
     * @param bool $override [optional] <p>false: Add to existing methods</p><p>true: Override existing methods</p>
     * @return $this
     */
    public function setAnyHttpMethods(array $methods, bool $override = true): self
    {
        $this->anyMethods = $override ? $methods : [...$this->anyMethods, ...$methods];
        return $this;
    }

    public function registerRoutes(): void
    {
        if ($this->hasCacheFile) {
            $this->routeCollector->collect(true);
        } else {
            $classes = (new ClassFinder())->findInDir(
                $this->directories,
                $this->getFilePattern($this->fileNamePattern[0], $this->fileNamePattern[1])
            );

            $parser = new RouteParser(
                $classes,
                $this->groups,
                $this->anyMethods,
                $this->getApiVersions(),
                $this->defaultRoutePriority
            );

            $this->routeCollector
                ->setParser($parser)
                ->prioritizeRoutes($this->defaultRoutePriority !== null)
                ->collect();
        }
    }

    /**
     * @return SlimRoute[]
     */
    public function getRoutes(): array
    {
        return $this->routeCollector->getRoutes();
    }

    /**
     * @return VersionConfiguration[]
     */
    private function getApiVersions(): array
    {
        if (count($this->apiVersions) === 0) {
            return [new VersionConfiguration(VersionConfiguration::NONE)];
        } else {
            $versionNone = array_filter($this->apiVersions, fn($v) => $v->version === VersionConfiguration::NONE);
            if (count($versionNone) === 0) {
                $this->apiVersions = [new VersionConfiguration(VersionConfiguration::NONE, default: false), ...$this->apiVersions];
            }
        }
        return array_reverse($this->apiVersions);
    }

    private function getFilePattern(string $fileName, string $fileExtension): string
    {
        return '/(' . $fileName . ')\.(' . $fileExtension . ')$/';
    }
}
