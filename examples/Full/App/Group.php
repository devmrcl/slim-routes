<?php

declare(strict_types=1);

namespace Mrcl\Examples\SlimRoutes\Full\App;

use Mrcl\Examples\SlimRoutes\Full\App\Middleware\GroupFirstMiddleware;
use Mrcl\Examples\SlimRoutes\Full\App\Middleware\GroupSecondMiddleware;
use Mrcl\Examples\SlimRoutes\Full\App\Middleware\ParentGroupFirstMiddleware;
use Mrcl\Examples\SlimRoutes\Full\App\Middleware\ParentGroupSecondMiddleware;
use Mrcl\SlimRoutes\Routing\GroupConfiguration;

final class Group
{
    public const USERS = 'users';

    /**
     * @var GroupConfiguration[]
     */
    public readonly array $get;

    public function __construct()
    {
        $internal  = new GroupConfiguration('__internal', '', [ParentGroupFirstMiddleware::class, ParentGroupSecondMiddleware::class]);
        $this->get = [
            self::USERS => new GroupConfiguration(self::USERS, 'users', [GroupFirstMiddleware::class, GroupSecondMiddleware::class], $internal),
        ];
    }
}
