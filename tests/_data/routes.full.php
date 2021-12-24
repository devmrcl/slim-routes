<?php

declare(strict_types=1);

use Mrcl\Examples\SlimRoutes\Full\App\Action\UserCreateAction;
use Mrcl\Examples\SlimRoutes\Full\App\Action\UserReadAction;
use Mrcl\Examples\SlimRoutes\Full\App\Controller\AdminsController;
use Mrcl\Examples\SlimRoutes\Full\App\Controller\AppController;
use Mrcl\Examples\SlimRoutes\Full\App\Middleware\ApiVersionFirstMiddleware;
use Mrcl\Examples\SlimRoutes\Full\App\Middleware\ApiVersionSecondMiddleware;
use Mrcl\Examples\SlimRoutes\Full\App\Middleware\GroupFirstMiddleware;
use Mrcl\Examples\SlimRoutes\Full\App\Middleware\GroupSecondMiddleware;
use Mrcl\Examples\SlimRoutes\Full\App\Middleware\ParentGroupFirstMiddleware;
use Mrcl\Examples\SlimRoutes\Full\App\Middleware\ParentGroupSecondMiddleware;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\ControllerFirstMiddleware;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\ControllerSecondMiddleware;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\RouteFirstMiddleware;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\RouteSecondMiddleware;
use Mrcl\SlimRoutes\Routing\RoutePriority;
use Mrcl\SlimRoutes\Routing\SlimRoute;

return [
    new SlimRoute(
        '/ping',
        ['GET'],
        AppController::class . ':ping',
        priority: RoutePriority::HIGHEST
    ),
    new SlimRoute(
        '/v2/users/{id:[0-9]+}',
        ['GET'],
        UserReadAction::class,
        [
            RouteSecondMiddleware::class,
            RouteFirstMiddleware::class,
            GroupSecondMiddleware::class,
            GroupFirstMiddleware::class,
            ParentGroupSecondMiddleware::class,
            ParentGroupFirstMiddleware::class,
            ApiVersionSecondMiddleware::class,
            ApiVersionFirstMiddleware::class
        ],
        priority: RoutePriority::HIGH
    ),
    new SlimRoute(
        '/v1/users/{id:[0-9]+}',
        ['GET'],
        UserReadAction::class,
        [
            RouteSecondMiddleware::class,
            RouteFirstMiddleware::class,
            GroupSecondMiddleware::class,
            GroupFirstMiddleware::class,
            ParentGroupSecondMiddleware::class,
            ParentGroupFirstMiddleware::class
        ],
        priority: RoutePriority::HIGH
    ),
    new SlimRoute(
        '/v2/users',
        ['POST'],
        UserCreateAction::class,
        [
            RouteSecondMiddleware::class,
            RouteFirstMiddleware::class,
            GroupSecondMiddleware::class,
            GroupFirstMiddleware::class,
            ParentGroupSecondMiddleware::class,
            ParentGroupFirstMiddleware::class,
            ApiVersionSecondMiddleware::class,
            ApiVersionFirstMiddleware::class
        ],
        priority: RoutePriority::NORMAL
    ),
    new SlimRoute(
        '/v1/users',
        ['POST'],
        UserCreateAction::class,
        [
            RouteSecondMiddleware::class,
            RouteFirstMiddleware::class,
            GroupSecondMiddleware::class,
            GroupFirstMiddleware::class,
            ParentGroupSecondMiddleware::class,
            ParentGroupFirstMiddleware::class
        ],
        priority: RoutePriority::NORMAL
    ),
    new SlimRoute(
        '/v2/users/admins',
        ['POST'],
        AdminsController::class . ':addAdmin',
        [
            RouteFirstMiddleware::class,
            ControllerSecondMiddleware::class,
            ControllerFirstMiddleware::class,
            GroupSecondMiddleware::class,
            GroupFirstMiddleware::class,
            ParentGroupSecondMiddleware::class,
            ParentGroupFirstMiddleware::class,
            ApiVersionSecondMiddleware::class,
            ApiVersionFirstMiddleware::class
        ],
        priority: RoutePriority::NORMAL
    ),
    new SlimRoute(
        '/v1/users/admins',
        ['POST'],
        AdminsController::class . ':addAdmin',
        [
            RouteFirstMiddleware::class,
            ControllerSecondMiddleware::class,
            ControllerFirstMiddleware::class,
            GroupSecondMiddleware::class,
            GroupFirstMiddleware::class,
            ParentGroupSecondMiddleware::class,
            ParentGroupFirstMiddleware::class
        ],
        priority: RoutePriority::NORMAL
    ),
    new SlimRoute(
        '/v2/users/admins',
        ['GET'],
        AdminsController::class . ':getAllAdmins',
        [
            RouteSecondMiddleware::class,
            RouteFirstMiddleware::class,
            ControllerSecondMiddleware::class,
            ControllerFirstMiddleware::class,
            GroupSecondMiddleware::class,
            GroupFirstMiddleware::class,
            ParentGroupSecondMiddleware::class,
            ParentGroupFirstMiddleware::class,
            ApiVersionSecondMiddleware::class,
            ApiVersionFirstMiddleware::class
        ],
        priority: RoutePriority::NORMAL
    ),
    new SlimRoute(
        '/v1/users/admins',
        ['GET'],
        AdminsController::class . ':getAllAdminsV1',
        [
            ControllerSecondMiddleware::class,
            ControllerFirstMiddleware::class,
            GroupSecondMiddleware::class,
            GroupFirstMiddleware::class,
            ParentGroupSecondMiddleware::class,
            ParentGroupFirstMiddleware::class
        ],
        priority: RoutePriority::NORMAL
    ),
    new SlimRoute(
        '/v2/users/admins/{id:[0-9]+}',
        ['GET'],
        AdminsController::class . ':getAdmin',
        [
            ControllerSecondMiddleware::class,
            ControllerFirstMiddleware::class,
            GroupSecondMiddleware::class,
            GroupFirstMiddleware::class,
            ParentGroupSecondMiddleware::class,
            ParentGroupFirstMiddleware::class,
            ApiVersionSecondMiddleware::class,
            ApiVersionFirstMiddleware::class
        ],
        priority: RoutePriority::NORMAL
    ),
    new SlimRoute(
        '/v1/users/admins/{id:[0-9]+}',
        ['GET'],
        AdminsController::class . ':getAdmin',
        [
            ControllerSecondMiddleware::class,
            ControllerFirstMiddleware::class,
            GroupSecondMiddleware::class,
            GroupFirstMiddleware::class,
            ParentGroupSecondMiddleware::class,
            ParentGroupFirstMiddleware::class
        ],
        priority: RoutePriority::NORMAL
    ),
    new SlimRoute(
        '/hello',
        ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'LINK', 'UNLINK'],
        AppController::class . ':hello',
        priority: RoutePriority::LOWEST
    )
];
