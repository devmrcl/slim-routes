<?php

declare(strict_types=1);

use Mrcl\Examples\SlimRoutes\Full\App\ApiVersion;
use Mrcl\Examples\SlimRoutes\Full\App\Group;
use Mrcl\Examples\SlimRoutes\Minimal\App\Middleware\GlobalMiddleware;
use Mrcl\SlimRoutes\SlimRoutes;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../vendor/autoload.php';

$app = AppFactory::create();

$apiVersions = new ApiVersion();
$groups      = new Group();

$sr = new SlimRoutes(
    $app,
    __DIR__ . '/App'
);
$sr
    //->enableCache(__DIR__ . '/cache/sr.cache')
    ->setFileNamePattern('.+(Action|Controller)')
    ->enableRoutePrioritization()
    ->setAnyHttpMethods(['LINK', 'UNLINK'], false)
    ->addApiVersion($apiVersions->get[ApiVersion::NONE])
    ->addApiVersion($apiVersions->get[ApiVersion::V1])
    ->addApiVersion($apiVersions->get[ApiVersion::V2])
    ->addGroup($groups->get[Group::USERS])
    ->registerRoutes();

$app->add(GlobalMiddleware::class);
$app->addErrorMiddleware(true, false, false);

$app->run();
