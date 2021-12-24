<?php

declare(strict_types=1);

namespace Mrcl\Examples\SlimRoutes\Minimal\App\Action;

use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\RouteFirstMiddleware;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\RouteSecondMiddleware;
use Mrcl\SlimRoutes\Attribute\Route;
use Mrcl\SlimRoutes\Routing\HttpMethod;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

#[Route(
    pattern: 'users',
    method: HttpMethod::POST,
    middleware: [RouteFirstMiddleware::class, RouteSecondMiddleware::class]
)]
class UserCreateAction
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        echo '=> UserCreateAction<br>';
        return $response;
    }
}
