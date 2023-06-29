<?php

namespace Modules\Order\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Order\Exceptions\AlreadyExistsException;
use Modules\Order\Exceptions\OrderPermissionDeniedException;
use Modules\Order\Exceptions\OrderStatusNotAllow;
use Modules\Order\Models\Order;

class CheckOrderCreatedByAuthUser
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param array $statuses
     * @return mixed
     * @throws OrderPermissionDeniedException
     */
    public function handle(Request $request, Closure $next, ...$statuses): mixed
    {
        /** @var Order|null $order */
        $order = $request->route('order');

        // Check if order status is allowed
        if ($order->user_id !== $request->user()->id) {
           throw new OrderPermissionDeniedException('You are not allowed to do this action');
        }

        return $next($request);
    }
}
