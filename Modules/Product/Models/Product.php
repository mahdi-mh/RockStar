<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\Database\factories\ProductFactory;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $visible = [
        'name',
        'description',
        'price',
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
}
