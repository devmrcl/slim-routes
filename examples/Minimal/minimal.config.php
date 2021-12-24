<?php

declare(strict_types=1);

use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\GlobalMiddleware;
use Mrcl\SlimRoutes\SlimRoutes;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../vendor/autoload.php';

$app = AppFactory::create();

$sr = new SlimRoutes(
    $app,
    __DIR__ . '/App'
);
$sr
    //->enableCache(__DIR__ . '/cache/sr.cache')
    ->registerRoutes();

$app->add(GlobalMiddleware::class);
$app->addErrorMiddleware(true, false, false);

$app->run();
