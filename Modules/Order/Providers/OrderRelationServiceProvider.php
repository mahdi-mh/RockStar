<?php

namespace Modules\Order\Providers;

use \Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;

class OrderRelationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap service provider
     *
     * @return void
     */
    public function boot(): void
    {
        /**
         * User relation
         *
         * Resolved relation using closure
         * @var User $user
         */
        User::resolveRelationUsing('order', static function (Model|User $user) {
            return $user->hasMany(Order::class);
        });

        /**
         * Order relation
         *
         * Resolved relation using closure
         * @var Order $order
         */
        Product::resolveRelationUsing('order', static function (Model|Product $product) {
            return $product->belongsToMany(Order::class)
                ->withPivot(['details']);
        });
    }
}
