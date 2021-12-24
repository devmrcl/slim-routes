<?php

declare(strict_types=1);

namespace Mrcl\Examples\SlimRoutes\Minimal\App\Action;

use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\RouteFirstMiddleware;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\RouteSecondMiddleware;
use Mrcl\SlimRoutes\Attribute\Route;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

#[Route(
    pattern:'users/{id:[0-9]+}',
    middleware: [RouteFirstMiddleware::class, RouteSecondMiddleware::class]
)]
class UserReadAction
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        echo '=> UserReadAction<br>';
        return $response;
    }
}
