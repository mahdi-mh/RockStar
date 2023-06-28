<?php

namespace Modules\Order\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rules\Enum;
use Modules\Order\Enums\OrderConsumeLocation;
use Modules\Order\Models\Order;
use Modules\Order\Resources\OrderCollectionResource;
use Throwable;
use Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(Request $request)
    {
        Validator::validate($request->all(), [
            'per_page' => 'integer',
            'page' => 'integer',
        ]);

        /** @var User|Model $user */
        $user = $request->user();

        return new OrderCollectionResource($user->order()->paginate($request->get('per_page', 10)));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return
     * @throws Throwable
     */
    public function store(Request $request)
    {
        $validatedData = Validator::validate($request->all(), [
            'consume_location' => ['required', 'string', new Enum(OrderConsumeLocation::class)],
            'address' => ['required_if:consume_location,' . OrderConsumeLocation::TAKE_AWAY->value, 'string', 'min:3', 'max:255']
        ]);

        $order = new Order($validatedData);

        $request->user()
            ->order()->save($order);

        return $order;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('order::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('order::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
