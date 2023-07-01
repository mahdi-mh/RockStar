<?php

namespace Modules\Order\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use JsonException;
use Modules\Order\Enums\OrderConsumeLocation;
use Modules\Order\Enums\OrderStatus;
use Modules\Order\Models\Order;
use Modules\Order\Resources\OrderCollectionResource;
use Modules\Order\Resources\OrderJsonResource;
use Modules\Product\Models\Product;
use Throwable;
use Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(Request $request): OrderCollectionResource
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
     * @return OrderJsonResource
     */
    public function store(Request $request): OrderJsonResource
    {
        $validatedData = Validator::validate($request->all(), [
            'consume_location' => ['required', 'string', new Enum(OrderConsumeLocation::class)],
            'address' => ['required_if:consume_location,' . OrderConsumeLocation::TAKE_AWAY->value, 'string', 'min:3', 'max:255']
        ]);

        $order = new Order($validatedData);

        $request->user()
            ->order()->save($order);

        return new OrderJsonResource($order);
    }

    /**
     * Add a product to order.
     *
     * @param Request $request
     * @param Order $order
     * @return JsonResponse
     * @throws Throwable
     */
    public function addProduct(Request $request, Order $order): JsonResponse
    {
        $validatedData = Validator::validate($request->all(), [
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'details' => ['json'],
        ]);

        $product = Product::findOrFail($validatedData['product_id']);

        $productDetails = $this->detailsValidation($product, $validatedData['details'] ?? null);

        $order->products()
            ->attach($product->id, [
                'details' => $productDetails ?? null,
            ]);

        $order->calculateTotalPrice();

        return response()->json([
            'message' => 'Product added to order successfully.',
            'data' => new OrderJsonResource($order),
        ]);
    }

    public function deleteProduct(Request $request, Order $order)
    {
        $validatedData = Validator::validate($request->all(), [
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
        ]);

        $order->products()
            ->detach($validatedData['product_id']);

        return response()->json([
            'message' => 'Product successfully deleted from this order.',
        ]);
    }

    public function getOrder(Order $order)
    {
        $order->updateOrFail([
            'status' => OrderStatus::PREPARATION->value,
        ]);

        return response()->json([
            'message' => 'Product successfully submitted , please wait for preparing.',
        ]);
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
        $details = [];

        foreach ($givenDetails as $detail) {
            if ($productDetails->contains('id', $detail->id)) {
                $productDetail = $productDetails->where('id', $detail->id)->first();

                if (!in_array($detail->value, $productDetail->options->toArray(), true)) {
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
