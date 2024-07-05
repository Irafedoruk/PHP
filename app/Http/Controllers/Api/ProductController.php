<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
class ProductController extends Controller
{
    public function getAll(Request $request): \Illuminate\Http\JsonResponse
    {
        $id=$request->query("categoryId");
        $items = Product::with(["category","product_images"])
            ->where("category_id", "=",$id)->get();
        return response()->json($items) ->header('Content-Type', 'application/json; charset=utf-8');
    }

    public function store(Request $request)
    {
        $product = Product::create($request->only(['name', 'description', 'price', 'quantity', 'category_id']));

        if($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('public/products');
                ProductImage::create([
                    'name' => basename($path),
                    'product_id' => $product->id,
                    'priority' => 0 // Можливо, вам знадобиться логіка для визначення пріоритету
                ]);
            }
        }

        return response()->json($product, 201);
    }

    public function destroy($id) {
        $product = Product::find($id);
        if($product) {
            $product->delete();
            return response()->json(['message' => 'Product deleted successfully'], 200);
        }
        return response()->json(['message' => 'Product not found'], 404);
    }
}
