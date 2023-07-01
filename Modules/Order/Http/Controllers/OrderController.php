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
     * List
     *
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return OrderCollectionResource
     *
     * @group Order
     *
     * @urlParam per_page int The number of products per page.
     * @urlParam page int The page number.
     *
     * @responseFile status=200 scenario="Success" Modules/Order/Storage/example-response/list-200.json
     * @responseFile status=401 scenario="Unauthenticated" storage/example-response/auth/unauth-401.json
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
     * Store
     *
     * Store an order.
     *
     * @param Request $request
     * @return OrderJsonResource
     *
     * @group Order
     *
     * @bodyParam consume_location string required Consume of location in [in_shop, take_away] Example: in_shop
     * @bodyParam address string Delivered address in required_if `consume_location` is `take_away`
     *
     * @responseFile status=201 scenario="Successfully created" Modules/Order/Storage/example-response/store-201.json
     * @responseFile status=406 scenario="Successfully created" Modules/Order/Storage/example-response/store-201.json
     * @responseFile status=401 scenario="When this user already have active order" Modules/Order/Storage/example-response/store-406.json
     * @responseFile status=401 scenario="Unauthenticated" storage/example-response/auth/unauth-401.json
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
     * Add product
     *
     * Add a product to order.
     *
     * @param Request $request
     * @param Order $order
     * @return JsonResponse
     * @throws Throwable
     *
     * @group Order
     *
     * @bodyParam product_id integer required Product id that want to add to this order.
     * @bodyParam details string Details of product in json format Example: [{"id":1,"value":"skim"}]
     *
     * @responseFile status=200 scenario="Successfully fetched" Modules/Order/Storage/example-response/add-product-200.json
     * @responseFile status=422 scenario="Invalid data" Modules/Order/Storage/example-response/add-product-422.json
     * @responseFile status=403 scenario="Permission denied" Modules/Order/Storage/example-response/add-product-403.json
     * @responseFile status=406 scenario="When order status is not ordering" Modules/Order/Storage/example-response/add-product-406.json
     * @responseFile status=401 scenario="Unauthenticated" storage/example-response/auth/unauth-401.json
     */
    public function addProduct(Request $request, Order $order): JsonResponse
    {
        $validatedData = Validator::validate($request->all(), [
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'details' => ['string', 'json'],
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
            'data' => new OrderJsonResource($order->refresh()),
        ]);
    }

    /**
     * Delete product
     *
     * Delete a product from order.
     *
     * @param Request $request
     * @param Order $order
     * @return JsonResponse
     *
     * @group Order
     *
     * @bodyParam product_id integer required Product id that want to add to this order.
     *
     * @responseFile status=200 scenario="Successfully deleted" Modules/Order/Storage/example-response/delete-product-200.json
     * @responseFile status=422 scenario="Invalid data" Modules/Order/Storage/example-response/add-product-422.json
     * @responseFile status=403 scenario="Permission denied" Modules/Order/Storage/example-response/add-product-403.json
     * @responseFile status=406 scenario="When order status is not ordering" Modules/Order/Storage/example-response/add-product-406.json
     * @responseFile status=401 scenario="Unauthenticated" storage/example-response/auth/unauth-401.json
     */
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

    /**
     * Prepare
     *
     * Request to prepare this order
     *
     * @param Order $order
     * @return JsonResponse
     * @throws Throwable
     *
     * @group Order
     *
     * @responseFile status=200 scenario="Successfully submitted" Modules/Order/Storage/example-response/prepare-200.json
     * @responseFile status=403 scenario="Permission denied" Modules/Order/Storage/example-response/add-product-403.json
     * @responseFile status=406 scenario="When order status is not ordering" Modules/Order/Storage/example-response/add-product-406.json
     * @responseFile status=401 scenario="Unauthenticated" storage/example-response/auth/unauth-401.json
     */
    public function prepareOrder(Order $order)
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
