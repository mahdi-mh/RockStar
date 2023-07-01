<?php

namespace Modules\Product\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\ProductRepositoryInterface;

class ProductServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected string $moduleName = 'Product';

    /**
     * @var string $moduleNameLower
     */
    protected string $moduleNameLower = 'product';

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
        // Register the routes
        $this->app->register(RouteServiceProvider::class);

        // Bind repository
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
    }
}
