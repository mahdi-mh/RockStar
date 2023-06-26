<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Product\Database\factories\ProductFactory;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $visible = [
        'id',
        'name',
        'description',
        'price',
        'details'
    ];

    /**
     * Override default factory path, reference
     *
     * @return ProductFactory
     */
    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    /**
     * Product details
     *
     * @return BelongsToMany
     */
    public function details(): BelongsToMany
    {
        return $this->belongsToMany(ProductDetail::class, 'product_product_detail', 'product_id', 'detail_id');
    }
}
