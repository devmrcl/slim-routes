<?php

declare(strict_types=1);

namespace Mrcl\Examples\SlimRoutes\Full\App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ApiVersionSecondMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        echo 'ApiVersionSecondMiddleware<br>';
        $res = $handler->handle($request);
        echo 'ApiVersionSecondMiddleware<br>';
        return $res;
    }
}
