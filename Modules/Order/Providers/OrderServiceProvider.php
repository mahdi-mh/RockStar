<?php

namespace Modules\Order\Providers;

use Illuminate\Support\ServiceProvider;
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
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(OrderRelationServiceProvider::class);

        // Bind repository
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
    }
}
