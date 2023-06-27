<?php

namespace Modules\Order\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Order\Models\Order;

class OrderCollectionResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(fn(Order|Model $order) => new OrderJsonResource($order));
    }
}
