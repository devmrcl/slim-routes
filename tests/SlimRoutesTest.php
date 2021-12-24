<?php

declare(strict_types=1);

namespace Mrcl\Tests\SlimRoutes;

use Mrcl\Examples\SlimRoutes\Full\App\ApiVersion;
use Mrcl\Examples\SlimRoutes\Full\App\Group;
use Mrcl\SlimRoutes\SlimRoutes;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Factory\AppFactory;

use function count;

class SlimRoutesTest extends TestCase
{
    public function testRouteRegistration(): void
    {
        $app         = AppFactory::create($this->createMock(ResponseFactoryInterface::class));
        $apiVersions = new ApiVersion();
        $groups      = new Group();
        $sr          = new SlimRoutes($app, __DIR__ . '/../examples/Full/App');
        $sr->setFileNamePattern('.+(Action|Controller)')
            ->enableRoutePrioritization()
            ->setAnyHttpMethods(['LINK', 'UNLINK'], false)
            ->addApiVersion($apiVersions->get[ApiVersion::NONE])
            ->addApiVersion($apiVersions->get[ApiVersion::V1])
            ->addApiVersion($apiVersions->get[ApiVersion::V2])
            ->addGroup($groups->get[Group::USERS])
            ->registerRoutes();

        self::assertEquals(
            require '_data/routes.full.php',
            $sr->getRoutes()
        );
        self::assertCount(
            count($sr->getRoutes()),
            $app->getRouteCollector()->getRoutes()
        );
    }
}
