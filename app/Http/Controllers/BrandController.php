<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    public function getBrands()
    {
        $getCategories = Brands::get();
        return $getCategories;
    }
    public function loadProductsInBrand(Request $request)
    {
        $data = $request->input();

        $getProductsInBrand =  DB::table('brands')
        ->join('products', 'brands.id', '=', 'products.brand_id')
        ->where('brands.name_str',  $data['brandStr'])
        ->select('brands.*', 'products.*')
        ->get();
        
        return $getProductsInBrand;
    }
}
