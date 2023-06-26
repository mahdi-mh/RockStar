<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Product\Models\Product;

class ProductController extends Controller
{
    /**
     * @return mixed
     */
    public function index()
    {
        return Product::paginate();
    }
}
