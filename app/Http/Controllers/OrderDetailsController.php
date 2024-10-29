<?php

namespace App\Http\Controllers;

use App\Models\OrderDetails;
use Illuminate\Http\Request;

class OrderDetailsController extends Controller
{
    public function createOrderDetails(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->input();
            $orderID = $data['orderID'];
            $productID = $data['productID'];
            $quantity = $data['quantity'];
            $price = $data['price'];

            $order_details = OrderDetails::create([
                'order_id' => $orderID ,
                'product_id' => $productID,
                'quantity' => $quantity,
                'price' => $price
            ]);
            $order_details->save();

            return response()->json([
                'status' => true,
                'message' => 'Add order details successful'
            ], 201);
        }
    }
}
