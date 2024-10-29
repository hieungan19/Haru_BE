<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavouriteController extends Controller
{
    public function getProductsInFavs(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->input();
            $userID = $data['userID'];

            $getProductsInFavs =  DB::table('favourites')
            ->join('products', 'favourites.product_id', '=', 'products.id')
            ->where('user_id',  $userID)
            ->select('favourites.*', 'products.*')
            ->get();
            
            $productsArr = [];
            foreach ($getProductsInFavs as $product) {
                $productsArr[] = $product;
            }
            return $productsArr;
        }
    }
    public function checkFaved(Request $request)
    {
            $data = $request->input();
            $userID = $data['userID'];
            $productID = $data['productID'];

            $getProductsInFavs =  DB::table('favourites')
            ->where('user_id',  $userID)
            ->where('product_id',  $productID)
            ->get();
            
            $flag = 0;
            foreach ($getProductsInFavs as $product) {
                $flag = 1;
            }
            return $flag;
    }
    public function addToFavs(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->input();
            $userID = $data['userID'];
            $productID = $data['productID'];
            
            $getProductsInFavs =  DB::table('favourites')->where('user_id',  $userID)->where('product_id',  $productID)->get();
            $flag = false;
            foreach ($getProductsInFavs as $product) {
                $flag = true;
            }
            
            if(!$flag){
                $favs = Favourite::create([
                    'user_id' => $userID,
                    'product_id' => $productID
                ]);
                $favs->save();
                return response()->json([
                    'status' => true,
                    'message' => 'Add to favourite cart successful'
                ], 201);
            }
        }
    }
    public function removeFromFavs(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->input();
            $userID = $data['userID'];
            $productID = $data['productID'];

            DB::table('favourites')
            ->where('user_id',  $userID)
            ->where('product_id',  $productID)
            ->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Remove from favourite cart successful'
            ], 201);
        }
    }
}
