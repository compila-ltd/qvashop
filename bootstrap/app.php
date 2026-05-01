<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        then: function ($router) {
            // Cargar primero las rutas específicas ANTES que web.php (que tiene la catch-all /{slug})
            if (file_exists(base_path('routes/admin.php'))) {
                Route::middleware('web')->group(base_path('routes/admin.php'));
            }
            if (file_exists(base_path('routes/seller.php'))) {
                Route::middleware('web')->group(base_path('routes/seller.php'));
            }
            if (file_exists(base_path('routes/seller_package.php'))) {
                Route::middleware('web')->group(base_path('routes/seller_package.php'));
            }
            if (file_exists(base_path('routes/api_seller.php'))) {
                Route::middleware('api')->group(base_path('routes/api_seller.php'));
            }
            
            // Web routes AL FINAL (tiene catch-all /{slug})
            Route::middleware('web')->group(base_path('routes/web.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware personalizado
        $middleware->alias([
            'app_language' => \App\Http\Middleware\AppLanguage::class,
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'seller' => \App\Http\Middleware\IsSeller::class,
            'customer' => \App\Http\Middleware\IsCustomer::class,
            'user' => \App\Http\Middleware\IsUser::class,
            'unbanned' => \App\Http\Middleware\IsUnbanned::class,
            'checkout' => \App\Http\Middleware\CheckoutMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // Web middleware group
        $middleware->web(append: [
            \App\Http\Middleware\Language::class,
            \App\Http\Middleware\CheckForMaintenanceMode::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
