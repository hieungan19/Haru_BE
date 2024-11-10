<?php

namespace App\Http\Controllers;

use App\Models\OrderDetails;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $cloudinary;
    public function getAllProducts()
    {
        $getProducts = DB::table('products')
        ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
        ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
        ->select('products.*', 'categories.name as category_name', 'brands.name as brand_name')
        ->get();
        return $getProducts;
    }
    public function getProducts()
    {
        $getProducts = DB::table('products')
        ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
        ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
        ->where('products.status', 1)
        ->select('products.*', 'categories.name as category_name', 'brands.name as brand_name')
        ->get();
        return $getProducts;
    }
    public function getProductInf(Request $request)
    {
        $data = $request->input();
        $getProduct = Product::where('id',  $data['productID'])->get();
        return $getProduct;
    }
    public function updateProduct(Request $request)
    {
            $data = $request->input();

            $productCount = Product::where('id', $data['id'])->count();
            if ($productCount > 0) {
                $updateData = array_filter($request->all());

                Product::where('id', $data['id'])->update($updateData);
                // Product::where('id', $data['id'])->update(['image' =>$request->all()['image']]);

                $productDetails = Product::where('id', $data['id'])->first();

                return response()->json([
                    'productDetails' => $productDetails,
                    'status' => true,
                    'message' => 'Product update successfully'
                ], 201);
            } else {
                return response()->json(['status' => false, 'message' => "Product does not exists"], 422);
            }
    }
    public function uploadImageProduct(Request $request)
    {
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $request->file('image')->store('images');

            $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();

            // $newProduct = new Product();

            // $newProduct->image = $uploadedFileUrl;
            // $newProduct->save();

            if ($uploadedFileUrl) 
                return response()->json(['url' => $uploadedFileUrl], 200);
            else  
                return back()->with('status', 'Failed to upload image');
        } else {
            return back()->with('status', 'Failed to upload image');
        }
    }
    public function addProduct(Request $request)
    {
            $data = $request->input();

            // $productCount =  Product::where('name', $data['name'])->count();
            // if ($productCount > 0) {
            //     return response()->json(['status' => false, 'message' => "Product is existed"], 422);
            // }
            // else{
                $product = Product::create([
                    'name' => $data['name'],
                    'category_id' => $data['category_id'],
                    'brand_id' => $data['brand_id'],
                    'price' => $data['price'],
                    'inventory_quantity' => $data['inventory_quantity'],
                    'image' => $data['image'],
                    'quantity_sold' => 0,
                    'status' => true,
                    'star' => 0
                ]);
                $product->save();

                $productDetails = Product::where('name', $data['name'])->first();

                return response()->json([
                    'productDetails' => $productDetails,
                    'status' => true,
                    'message' => 'Add product successful'
                ], 201);
            // }
    }
    public function deleteProduct(Request $request)
    {
            $data = $request->input();
        $orderWithProducts = OrderDetails::where('product_id',  $data['id'])->first();
            
        if ($orderWithProducts) {
            // Nếu id của address đã được sử dụng trong bảng order, cập nhật status của address thành 0
            $product = Product::find( $data['id']);
            if ($product) {
                $product->status = 0;
                $product->save();

                return response()->json(['message' => 'Product status updated to 0'], 204);
            } else {
                return response()->json(['error' => 'Product not found'], 404);
            }
        } else {
            // Nếu id của address không được sử dụng trong bảng order, xóa address
            $product = Product::find( $data['id']);
            if ($product) {
                $product->delete();

                return response()->json(['message' => 'Product deleted'], 200);
            } else {
                return response()->json(['error' => 'Product not found'], 404);
            }
        }
    }
    public function searchProducts($text)
    {
        $searchProducts =  Product::where('name', 'LIKE', '%' . $text . '%')
                                ->where('status', 1)
                                ->get();
        return $searchProducts;
    }
}
