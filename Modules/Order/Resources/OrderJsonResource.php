<?php

namespace Modules\Order\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Product\Models\Product;

class OrderJsonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'consume_location' => $this->consume_location,
            'address' => $this->address,
            'price' => $this->price,
            'products' => $this->products->map(function (Product|Model $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'details' => json_decode($product->pivot->details ?? '{}', true, 512, JSON_THROW_ON_ERROR)
                ];
            }),
        ];
    }
}
