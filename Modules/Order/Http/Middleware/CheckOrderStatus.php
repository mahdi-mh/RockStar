<?php

namespace Modules\Order\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Order\Exceptions\AlreadyExistsException;
use Modules\Order\Exceptions\OrderStatusNotAllow;
use Modules\Order\Models\Order;

class CheckOrderStatus
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param array $statuses
     * @return mixed
     * @throws OrderStatusNotAllow
     */
    public function handle(Request $request, Closure $next, ...$statuses): mixed
    {
        /** @var Order|null $order */
        $order = $request->route('order');

        // Check if order status is allowed
        if (!is_int(collect($statuses)->search($order->status))) {
           throw new OrderStatusNotAllow('This order status not allow for this action');
        }

        return $next($request);
    }
}
