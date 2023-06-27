<?php

namespace Modules\Order\Enums;

enum OrderStatus:string
{
    case ORDERING = "ordering";
    case PREPARATION = "preparation";
    case READY = "ready";
    case DELIVERED = "delivered";
    case CANCELED = "canceled";
}
