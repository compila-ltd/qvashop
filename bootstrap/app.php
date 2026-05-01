<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        then: function ($router) {
            // Rutas adicionales
            if (file_exists(base_path('routes/admin.php'))) {
                Route::middleware('web')->group(base_path('routes/admin.php'));
            }
            if (file_exists(base_path('routes/seller.php'))) {
                Route::middleware('web')->group(base_path('routes/seller.php'));
            }
            if (file_exists(base_path('routes/affiliate.php'))) {
                Route::middleware('web')->group(base_path('routes/affiliate.php'));
            }
            if (file_exists(base_path('routes/auction.php'))) {
                Route::middleware('web')->group(base_path('routes/auction.php'));
            }
            if (file_exists(base_path('routes/club_points.php'))) {
                Route::middleware('web')->group(base_path('routes/club_points.php'));
            }
            if (file_exists(base_path('routes/delivery_boy.php'))) {
                Route::middleware('web')->group(base_path('routes/delivery_boy.php'));
            }
            if (file_exists(base_path('routes/offline_payment.php'))) {
                Route::middleware('web')->group(base_path('routes/offline_payment.php'));
            }
            if (file_exists(base_path('routes/otp.php'))) {
                Route::middleware('web')->group(base_path('routes/otp.php'));
            }
            if (file_exists(base_path('routes/pos.php'))) {
                Route::middleware('web')->group(base_path('routes/pos.php'));
            }
            if (file_exists(base_path('routes/refund_request.php'))) {
                Route::middleware('web')->group(base_path('routes/refund_request.php'));
            }
            if (file_exists(base_path('routes/seller_package.php'))) {
                Route::middleware('web')->group(base_path('routes/seller_package.php'));
            }
            if (file_exists(base_path('routes/wholesale.php'))) {
                Route::middleware('web')->group(base_path('routes/wholesale.php'));
            }
            if (file_exists(base_path('routes/api_seller.php'))) {
                Route::middleware('api')->group(base_path('routes/api_seller.php'));
            }
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
            'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
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
