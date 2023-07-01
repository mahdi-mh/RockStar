<?php

namespace Modules\Order\Providers;

use App;
use Illuminate\Support\ServiceProvider;
use Modules\Order\Models\Order;
use Modules\Order\Observers\OrderModelObserver;
use Modules\Order\Repositories\OrderRepository;
use Modules\Order\Repositories\OrderRepositoryInterface;

class OrderServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected string $moduleName = 'Order';

    /**
     * @var string $moduleNameLower
     */
    protected string $moduleNameLower = 'order';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        // Register observer
        if (!App::environment('testing')) {
            Order::observe(OrderModelObserver::class);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        // Register routes
        $this->app->register(RouteServiceProvider::class);

        // Register relation
        $this->app->register(OrderRelationServiceProvider::class);

        // Bind repository
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
    }
}
