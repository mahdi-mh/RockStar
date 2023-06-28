<?php

namespace Modules\Order\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Order\Database\factories\OrderFactory;
use Modules\Product\Models\Product;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['products'];

    protected $visible = [
        'id',
        'status',
        'consume_location',
        'address',
        'price',
    ];

    /**
     * The factory instance for the model.
     *
     * @return OrderFactory
     */
    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }

    /**
     * Relation to User
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation to products
     *
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['details']);
    }
}
