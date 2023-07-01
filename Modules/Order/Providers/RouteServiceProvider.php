<?php

namespace Modules\Order\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Modules\Order\Http\Middleware\CheckOrderCreatedByAuthUser;
use Modules\Order\Http\Middleware\CheckOrderStatus;
use Modules\Order\Http\Middleware\CheckUserHaveNotActiveOrder;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        // Booting middlewares
        $router = app(Router::class);
        $router->aliasMiddleware('checkOrderCreatedByAuthUser', CheckOrderCreatedByAuthUser::class);
        $router->aliasMiddleware('checkOrderStatus', CheckOrderStatus::class);
        $router->aliasMiddleware('checkUserHaveNotActiveOrder', CheckUserHaveNotActiveOrder::class);
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map(): void
    {
        $this->mapApiRoutes();
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api/order')
            ->middleware(['api', 'auth:sanctum'])
            ->group(module_path('Order', '/Routes/api.php'));
    }
}
