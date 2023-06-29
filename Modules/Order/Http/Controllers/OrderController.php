<?php

namespace Modules\Order\Http\Controllers;

use App\Models\User;
use http\Encoding\Stream\Debrotli;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use JsonException;
use Modules\Order\Enums\OrderConsumeLocation;
use Modules\Order\Models\Order;
use Modules\Order\Resources\OrderCollectionResource;
use Modules\Product\Models\Product;
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
     * Add a product to order.
     *
     * @param Request $request
     * @param Order $order
     * @return JsonResponse
     * @throws Throwable
     */
    public function addProduct(Request $request, Order $order)
    {
        $validatedData = Validator::validate($request->all(), [
            'product_id' => ['required', 'integer', Rule::exists('products')],
            'details' => ['json'],
        ]);

        /** @var Product|Model $product */
        $product = Product::findOrFail($validatedData['product_id']);

        $productDetails = $this->detailsValidation($product, $validatedData['details'] ?? null);

        $order->products()
            ->attach($validatedData['product_id'], [
                'details' => $productDetails ?? null,
            ]);

        return response()->json([
            'message' => 'Product added to order successfully',
            'order' => $order->refresh(),
        ]);
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

    /**
     * Validate request details.
     *
     * @param Product $product
     * @param string|null $requestDetails
     * @return string|null
     * @throws JsonException
     */
    private function detailsValidation(Product $product, string|null $requestDetails = null): ?string
    {
        $productDetails = $product->details ?? collect();

        if (isset($requestDetails) && $productDetails->isNotEmpty()) {
            $givenDetails = json_decode($requestDetails, false, 512, JSON_THROW_ON_ERROR);
            $jsonDetails = $this->prepareProductDetailToJson($productDetails, $givenDetails);
        }

        return $jsonDetails ?? null;
    }

    /**
     * Prepare product details.
     *
     * @param mixed $productDetails
     * @param $givenDetails
     * @return string
     * @throws JsonException
     */
    private function prepareProductDetailToJson(mixed $productDetails, $givenDetails): string
    {
        $productDetails->keyBy('id');
        $details = [];

        foreach ($givenDetails as $detail) {
            if ($productDetails->contains('id', $detail->id)) {
                $productDetail = $productDetails[$detail->id];

                if (!$productDetail->options->has($detail->value)) {
                    continue;
                }

                $details[] = [
                    'id ' => $detail->id,
                    'name' => $productDetail->name,
                    'value' => $detail->value,
                ];
            }
        }

        return json_encode($details, JSON_THROW_ON_ERROR);
    }
}
