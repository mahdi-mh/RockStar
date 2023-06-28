<?php

namespace Modules\Order\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Exceptions\AlreadyExistsException;

class CheckUserHaveNotActiveOrder
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws AlreadyExistsException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->user()->order()->where('status', OrderStatus::ORDERING)->count() > 0) {
            throw new AlreadyExistsException('This user already have active order');
        }

        return $next($request);
    }
}
