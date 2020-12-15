<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Http\Controllers\API\BaseController as BaseController;

class ProductController extends BaseController
{
    public function index()
    {
        $products = Product::all();
        return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');
    }

    public function imageUpload($file, $customName, $path)
    {
        $imageName = $customName . "." . $file->getClientOriginalExtension();
        if ($file->isValid()) {
            $file->move($path, $imageName);
            return $imageName;
        }
        return false;
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'required',
            'price' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            DB::beginTransaction();
            $product = Product::create([
                'title' => $request->title,
                'price' => $request->price,
                'description' => $request->description,
            ]);
            if ($request->get('image')) {
                $image = $request->get('image');
                $name = $product->id . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                \Image::make($request->get('image'))->save(public_path('images/') . $name);
                Product::query()->where('id', $product->id)->update([
                    'image' => $name,
                ]);
            }

            DB::commit();
            return $this->sendResponse(new ProductResource($product), 'Product created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error.', $e->getMessage());
        }

    }

    public function update(Request $request, Product $product)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => 'required',
            'description' => 'required',
            'price' => 'required',
        ]);
        try {
            DB::beginTransaction();
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            if ($request->get('image')) {
                $image = $request->get('image');
                $name = $product->id . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                \Image::make($request->get('image'))->save(public_path('images/') . $name);
            } else {
                $name = $product->image;
            }
            $product->title = $input['title'];
            $product->description = $input['description'];
            $product->price = $input['price'];
            $product->image = $name;
            $product->save();

            DB::commit();
            return $this->sendResponse(new ProductResource($product), 'Product update successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error.', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();
        return $this->sendResponse([], 'Product deleted successfully.');
    }
}
