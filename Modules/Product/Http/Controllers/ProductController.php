<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Product\Models\Product;
use Validator;

class ProductController extends Controller
{
    /**
     * List
     *
     * List all products as paginated
     *
     * @return mixed
     * @group Product
     *
     * @urlParam per_page int The number of products per page.
     * @urlParam page int The page number.
     * @responseFile status=200 scenario="Success" Modules/Product/Storage/example-response/list-200.json
     * @responseFile status=401 scenario="Unauthenticated" Modules/Product/Storage/example-response/list-401.json
     */
    public function index(Request $request)
    {
        Validator::validate($request->all(), [
            'per_page' => 'integer',
            'page' => 'integer',
        ]);

        return Product::with('details')->paginate($request->get('per_page', 10));
    }
}
