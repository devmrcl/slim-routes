<?php

declare(strict_types=1);

namespace Mrcl\Examples\SlimRoutes\Full\App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class AbstractAction
{
    protected Request  $request;
    protected Response $response;
    protected array    $args;

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request  = $request;
        $this->response = $response;
        $this->args     = $args;

        return $this->action();
    }

    abstract public function action(): Response;
}
