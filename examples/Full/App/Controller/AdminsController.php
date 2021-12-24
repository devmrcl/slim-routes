<?php

declare(strict_types=1);

namespace Mrcl\Examples\SlimRoutes\Full\App\Controller;

use Mrcl\Examples\SlimRoutes\Full\App\ApiVersion;
use Mrcl\Examples\SlimRoutes\Full\App\Group;
use Mrcl\Examples\SlimRoutes\Full\App\HttpMethod;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\ControllerFirstMiddleware;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\ControllerSecondMiddleware;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\RouteFirstMiddleware;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\RouteSecondMiddleware;
use Mrcl\SlimRoutes\Attribute\Controller;
use Mrcl\SlimRoutes\Attribute\Route;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

#[Controller(
    pattern: 'admins',
    middleware: [ControllerFirstMiddleware::class, ControllerSecondMiddleware::class],
    groupId: Group::USERS
)]
class AdminsController
{
    #[Route(
        method: HttpMethod::POST,
        middleware: RouteFirstMiddleware::class
    )]
    public function addAdmin(Request $request, Response $response, array $args): Response
    {
        echo '=> AdminsController:addAdmin<br>';
        return $response;
    }

    /*
     * ONLY v2
     */
    #[Route(
        middleware: [RouteFirstMiddleware::class, RouteSecondMiddleware::class],
        version: ApiVersion::V2
    )]
    public function getAllAdmins(Request $request, Response $response, array $args): Response
    {
        echo '=> AdminsController:getAllAdmins<br>';
        return $response;
    }

    /*
     * ONLY v1
     */
    #[Route(version: ApiVersion::V1)]
    public function getAllAdminsV1(Request $request, Response $response, array $args): Response
    {
        echo '=> AdminsController:getAllAdminsV1<br>';
        return $response;
    }

    #[Route('{id:[0-9]+}')]
    public function getAdmin(Request $request, Response $response, array $args): Response
    {
        echo '=> AdminsController:getAdmin<br>';
        return $response;
    }
}
