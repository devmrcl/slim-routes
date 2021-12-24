<?php

declare(strict_types=1);

namespace Mrcl\Examples\SlimRoutes\Full\App\Controller;

use Mrcl\Examples\SlimRoutes\Full\App\ApiVersion;
use Mrcl\Examples\SlimRoutes\Full\App\HttpMethod;
use Mrcl\SlimRoutes\Attribute\Controller;
use Mrcl\SlimRoutes\Attribute\Route;
use Mrcl\SlimRoutes\Routing\RoutePriority;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

#[Controller(version: ApiVersion::NONE)]
class AppController
{
    #[Route('ping')]
    public function ping(Request $request, Response $response, array $args): Response
    {
        return $response->withStatus(204);
    }

    #[Route('hello', method: HttpMethod::ANY, priority: RoutePriority::LOWEST)]
    public function hello(Request $request, Response $response, array $args): Response
    {
        return $response->withStatus(404);
    }
}
