<?php

declare(strict_types=1);

namespace Mrcl\Examples\SlimRoutes\Minimal\App\Controller;

use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\ControllerFirstMiddleware;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\ControllerSecondMiddleware;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\RouteFirstMiddleware;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\RouteSecondMiddleware;
use Mrcl\SlimRoutes\Attribute\Controller;
use Mrcl\SlimRoutes\Attribute\Route;
use Mrcl\SlimRoutes\Routing\HttpMethod;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

#[Controller(
    pattern: 'v1/users',
    middleware: [ControllerFirstMiddleware::class, ControllerSecondMiddleware::class]
)]
class UsersController
{
    #[Route(
        method: HttpMethod::POST,
        middleware: RouteFirstMiddleware::class
    )]
    public function addUser(Request $request, Response $response, array $args): Response
    {
        echo '=> UsersController:addUser<br>';
        return $response;
    }

    #[Route(middleware: [RouteFirstMiddleware::class, RouteSecondMiddleware::class])]
    public function getAllUsers(Request $request, Response $response, array $args): Response
    {
        echo '=> UsersController:getAllUsers<br>';
        return $response;
    }

    #[Route(
        pattern: '{id:[0-9]+}',
        name: 'get-user'
    )]
    public function getUser(Request $request, Response $response, array $args): Response
    {
        echo '=> UsersController:getUser<br>';
        return $response;
    }
}
