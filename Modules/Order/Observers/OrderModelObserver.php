<?php

namespace Modules\Order\Observers;

use App\Models\User;
use Modules\Order\Models\Order;
use Modules\Order\Notifications\NewOrderNotification;
use Modules\Order\Notifications\OrderChangeStatusNotification;

class OrderModelObserver
{
    public function created(Order $order)
    {
        try {
            /** @var User $user */
            $user = $order->user()->firstOrFail();
            \Log::info('order created , '. $order->id);
            $user->notify(new NewOrderNotification($order));
        } catch (\Throwable $exception) {
            \Log::error($exception->getMessage());
        }
    }

    /**
     * Handle the Product "updated" event.
     *
     * @param
     * @return
     */
    public function updated(Order $order)
    {
        try {
            /** @var User $user */
            $user = $order->user()->firstOrFail();
            $user->notify(new OrderChangeStatusNotification($order));
            \Log::info('order status changed , '. $order->id);
            return true;
        } catch (\Throwable $exception) {
            \Log::error($exception->getMessage());
        }
    }
}
