<?php

use Illuminate\Http\Request;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Http\Controllers\OrderController;
use Modules\Order\Http\Middleware\CheckOrderStatus;
use Modules\Order\Http\Middleware\CheckUserHaveNotActiveOrder;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('list', [OrderController::class, 'index']);

Route::middleware('checkUserHaveNotActiveOrder')
    ->post('store', [OrderController::class, 'store']);

Route::middleware([
    "checkOrderCreatedByAuthUser",
    "checkOrderStatus:" . OrderStatus::ORDERING->value,
])->post('{order}/add-product', [OrderController::class, 'addProduct']);

Route::middleware([
    "checkOrderCreatedByAuthUser",
    "checkOrderStatus:" . OrderStatus::ORDERING->value,
])->put('{order}/delete-product', [OrderController::class, 'deleteProduct']);
