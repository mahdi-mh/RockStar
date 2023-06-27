<?php

namespace Modules\Product\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\Product\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    private Product|Builder $product;

    public function __construct(Product|Builder|null $product = null)
    {
        $this->product = $product ?? Product::query();
    }

    /**
     * @inheritDoc
     */
    public function withDetails(): static
    {
        $this->product->with('details');
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->product->paginate($perPage);
    }
}
