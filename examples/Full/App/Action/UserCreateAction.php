<?php

declare(strict_types=1);

namespace Mrcl\Examples\SlimRoutes\Full\App\Action;

use Mrcl\Examples\SlimRoutes\Full\App\Group;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\RouteFirstMiddleware;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\RouteSecondMiddleware;
use Mrcl\SlimRoutes\Attribute\Route;
use Mrcl\SlimRoutes\Routing\HttpMethod;
use Psr\Http\Message\ResponseInterface as Response;

#[Route(
    method: HttpMethod::POST,
    middleware: [RouteFirstMiddleware::class, RouteSecondMiddleware::class],
    groupId: Group::USERS
)]
class UserCreateAction extends AbstractAction
{
    public function action(): Response
    {
        echo '=> UserCreateAction<br>';
        return $this->response;
    }
}
