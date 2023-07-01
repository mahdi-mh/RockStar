<?php

namespace Modules\Order\Repositories;

use App\Models\User;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;

interface OrderRepositoryInterface
{
    public function buildFromUser(User $user): static;
    public function setOrder(Order $order): static;

    public function paginate(int $perPage);
    public function store(array $data): Order;
    public function addProduct(Product $product, string $jsonProductDetails = null);
    public function deleteProduct(int $productId);
    public function prepare();
}
