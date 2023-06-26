<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

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
}
