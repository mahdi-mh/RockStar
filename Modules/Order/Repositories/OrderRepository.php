<?php

namespace Modules\Order\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;

class OrderRepository implements OrderRepositoryInterface
{
    private Order|Builder|HasMany $order;

    public function __construct(Order|Builder|null $order = null)
    {
        $this->order = $order ?? Order::query();
    }

    public static function build(Order|Builder|null $order = null): OrderRepository
    {
        return new self($order);
    }

    public function setOrder(Order $order): static
    {
        return new self($order);
    }

    public function buildFromUser(User $user): static
    {
        $this->order = $user->order();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->order->paginate($perPage);
    }

    public function store(array $data): Order
    {
        $newOrder = new Order($data);
        $this->order->save($newOrder);
        return $newOrder;
    }

    public function addProduct(Product $product, string $jsonProductDetails = null)
    {
        $this->order->products()
            ->attach($product->id, [
                'details' => $jsonProductDetails ?? null,
            ]);

        $this->order->calculateTotalPrice();
    }

    public function deleteProduct(int $productId)
    {
        $this->order->products()->detach($productId);
    }

    public function prepare()
    {
        $this->order->updateOrFail([
            'status' => OrderStatus::PREPARATION->value,
        ]);
    }
}
