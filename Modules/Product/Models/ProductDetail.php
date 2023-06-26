<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductDetail extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'options' => 'object',
    ];

    protected $visible = [
        'id',
        'name',
        'options',
    ];

    /**
     * Product relation
     *
     * @return BelongsToMany
     */
    public function product(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_product_detail', 'detail_id', 'product_id');
    }
}
