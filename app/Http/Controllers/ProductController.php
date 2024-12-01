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
        $getProduct = Product::where('id', $data['productID'])->get();
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
            // Lấy thông tin file
            $file = $request->file('image');
            $filePath = $file->getRealPath();
            $fileName = $file->getClientOriginalName();

            // Thông tin Storage Account
            $storageAccountname = env('AZURE_STORAGE_NAME');
            $accessKey = env('AZURE_STORAGE_KEY');
            $containerName = env('AZURE_STORAGE_CONTAINER');// Thay bằng tên container
            $blobName = $fileName; // Sử dụng tên file làm blob name
            $url = "https://{$storageAccountname}.blob.core.windows.net/{$containerName}/{$blobName}";

            // Gọi hàm uploadBlob
            $uploadResult = $this->uploadBlob($file, $filePath, $storageAccountname, $containerName, $blobName, $url, $accessKey);

            if ($uploadResult) {
                $uploadedFileUrl = $url;
                return response()->json(['url' => $uploadedFileUrl], 200);
            } else {
                return response()->json(['error' => 'Failed to upload image'], 500);
            }
        } else {
            return response()->json(['error' => 'Invalid image file'], 400);
        }
    }

    private function uploadBlob($file, $filePath, $storageAccountname, $containerName, $blobName, $url, $accessKey)
    {
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $handle = fopen($filePath, "r");
        $fileLen = filesize($filePath);

        $headerResource = "x-ms-blob-cache-control:max-age=3600\nx-ms-blob-type:BlockBlob\nx-ms-date:$date\nx-ms-version:2019-12-12";
        $urlResource = "/$storageAccountname/$containerName/$blobName";

        $arraysign = array();
        $arraysign[] = 'PUT';               /*HTTP Verb*/
        $arraysign[] = '';                  /*Content-Encoding*/
        $arraysign[] = '';                  /*Content-Language*/
        $arraysign[] = $fileLen;            /*Content-Length*/
        $arraysign[] = '';                  /*Content-MD5*/
        $arraysign[] = $file->getMimeType();/*Content-Type*/
        $arraysign[] = '';                  /*Date*/
        $arraysign[] = '';                  /*If-Modified-Since */
        $arraysign[] = '';                  /*If-Match*/
        $arraysign[] = '';                  /*If-None-Match*/
        $arraysign[] = '';                  /*If-Unmodified-Since*/
        $arraysign[] = '';                  /*Range*/
        $arraysign[] = $headerResource;     /*CanonicalizedHeaders*/
        $arraysign[] = $urlResource;        /*CanonicalizedResource*/

        $str2sign = implode("\n", $arraysign);

        $sig = base64_encode(hash_hmac('sha256', urldecode(utf8_encode($str2sign)), base64_decode($accessKey), true));
        $authHeader = "SharedKey $storageAccountname:$sig";

        $headers = [
            'Authorization: ' . $authHeader,
            'x-ms-blob-cache-control: max-age=3600',
            'x-ms-blob-type: BlockBlob',
            'x-ms-date: ' . $date,
            'x-ms-version: 2019-12-12',
            'Content-Type: ' . $file->getMimeType(),
            'Content-Length: ' . $fileLen
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_INFILE, $handle);
        curl_setopt($ch, CURLOPT_INFILESIZE, $fileLen);
        curl_setopt($ch, CURLOPT_UPLOAD, true);
        $result = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 201; // Trả về true nếu upload thành công
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
        $orderWithProducts = OrderDetails::where('product_id', $data['id'])->first();

        if ($orderWithProducts) {
            // Nếu id của address đã được sử dụng trong bảng order, cập nhật status của address thành 0
            $product = Product::find($data['id']);
            if ($product) {
                $product->status = 0;
                $product->save();

                return response()->json(['message' => 'Product status updated to 0'], 204);
            } else {
                return response()->json(['error' => 'Product not found'], 404);
            }
        } else {
            // Nếu id của address không được sử dụng trong bảng order, xóa address
            $product = Product::find($data['id']);
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
        $searchProducts = Product::where('name', 'LIKE', '%' . $text . '%')
            ->where('status', 1)
            ->get();
        return $searchProducts;
    }
}
