<?php

namespace Modules\Order\Enums;

enum OrderConsumeLocation:string
{
    case IN_SHOP = "in_shop";
    case TAKE_AWAY = "take_away";
}
