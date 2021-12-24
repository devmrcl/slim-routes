<?php

declare(strict_types=1);

namespace Mrcl\SlimRoutes\Util;

use function ltrim;

abstract class RouteUtil
{
    public static function harmonizePattern(?string ...$patterns): string
    {
        $pattern = '';
        foreach ($patterns as $str) {
            if ($str === null || $str === '') {
                continue;
            }
            $pattern .= '/' . ltrim($str, ' /');
        }
        return $pattern;
    }

    public static function harmonizeName(string $api, ?string $name): ?string
    {
        if ($name === null) {
            return null;
        }
        if ($api === '') {
            return $name;
        }
        return $api . '-' . $name;
    }
}
