<?php

declare(strict_types=1);

namespace Mrcl\Examples\SlimRoutes\Full\App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ParentGroupSecondMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        echo 'ParentGroupSecondMiddleware<br>';
        $res = $handler->handle($request);
        echo 'ParentGroupSecondMiddleware<br>';
        return $res;
    }
}
