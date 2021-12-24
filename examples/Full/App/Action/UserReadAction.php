<?php

declare(strict_types=1);

namespace Mrcl\Examples\SlimRoutes\Full\App\Action;

use Mrcl\Examples\SlimRoutes\Full\App\Group;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\RouteFirstMiddleware;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\RouteSecondMiddleware;
use Mrcl\SlimRoutes\Attribute\Route;
use Mrcl\SlimRoutes\Routing\RoutePriority;
use Psr\Http\Message\ResponseInterface as Response;

#[Route(
    pattern: '{id:[0-9]+}',
    middleware: [RouteFirstMiddleware::class, RouteSecondMiddleware::class],
    groupId: Group::USERS,
    priority: RoutePriority::HIGH
)]
class UserReadAction extends AbstractAction
{
    public function action(): Response
    {
        echo '=> UserReadAction<br>';
        return $this->response;
    }
}
