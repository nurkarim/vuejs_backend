<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Image;
use File;
class ProductController extends Controller
{
    public $imagesPath = '';
    public function index()
    {
        $products = Product::all();
        return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');
    }

    public function createDirectory()
    {
        $paths = [
            'image_path' => public_path('images/'),
        ];
        foreach ($paths as $key => $path) {
            if(!File::isDirectory($path)){
                File::makeDirectory($path, 0777, true, true);
            }
        }
        $this->imagesPath = $paths['image_path'];
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => 'required|string',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $product = Product::create($input);

        if($request->hasFile('image')) {

            $validator = Validator::make($input, [
                'image' => 'mimes:jpeg,jpg,png,gif|required|max:10000',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
                $this->createDirecrotory();
                $file=$request->image;
                $image = Image::make($file);
                $imageName = $product->id . '-' . $file->getClientOriginalName();
                $image->resize(300, 300);  // resize and save thumbnail
                $image->save($this->imagesPath . $imageName);
                //update product table
                $upload = Product::find($product->id);
                $upload->image = $imageName;
                $upload->save();
        }

        return $this->sendResponse(new ProductResource($product), 'Product created successfully.');
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }

        return $this->sendResponse(new ProductResource($product), 'Product retrieved successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'required|string',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'description' => 'required',
            'image' => 'mimes:jpeg,jpg,png,gif|max:10000',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $product->title = $input['title'];
        $product->price = $input['price'];
        $product->description = $input['description'];
        $product->save();

        return $this->sendResponse(new ProductResource($product), 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return $this->sendResponse([], 'Product deleted successfully.');
    }
}
