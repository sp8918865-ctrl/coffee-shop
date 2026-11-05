<?php

namespace App\Support;

class PanelRoute
{
    public static function name(string $adminRoute): string
    {
        if (request()->routeIs('barista.*') && str_starts_with($adminRoute, 'admin.')) {
            return 'barista.'.substr($adminRoute, strlen('admin.'));
        }

        return $adminRoute;
    }

    public static function url(string $adminRoute, mixed $parameters = []): string
    {
        return route(self::name($adminRoute), $parameters);
    }
}
