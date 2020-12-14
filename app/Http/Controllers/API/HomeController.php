<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends BaseController
{

    public function index()
    {

        $array=[
            'getProducts'=>ProductResource::collection(Product::query()->get())
            ];
        return $this->sendResponse($array, 'Product read success');
    }
}
