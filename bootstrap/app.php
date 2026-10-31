<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureDesktopDevice;
use App\Http\Middleware\EnsureCustomerScannedQr;
use App\Http\Middleware\EnsureCustomerApiToken;
use App\Http\Middleware\EnsurePublicQrEnabled;
use App\Http\Middleware\EnsureUserCanAccessAdminPanel;
use App\Http\Middleware\EnsureUserIsManager;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'desktop' => EnsureDesktopDevice::class,
            'manager' => EnsureUserIsManager::class,
            'admin.panel' => EnsureUserCanAccessAdminPanel::class,
            'customer.qr' => EnsureCustomerScannedQr::class,
            'customer.api' => EnsureCustomerApiToken::class,
            'public.qr' => EnsurePublicQrEnabled::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
