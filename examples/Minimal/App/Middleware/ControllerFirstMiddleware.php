<?php

declare(strict_types=1);

namespace Mrcl\Examples\SlimRoutes\Minimal\App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ControllerFirstMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        echo 'ControllerFirstMiddleware<br>';
        $res = $handler->handle($request);
        echo 'ControllerFirstMiddleware<br>';
        return $res;
    }
}
