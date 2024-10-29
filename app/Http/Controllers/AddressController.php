<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function getAddresses(Request $request)
    {
        $data = $request->all();
        $getAddress = Address::
                    where("user_id", $data['userID'])
                    ->where("status", 1)
                    ->get();
        return $getAddress;
    }
    public function addNewAddress(Request $request)
    {
        $data = $request->input();
        
        $address = Address::create([
            'user_id' => $data['userID'],
            'name' => $data['name'],
            'phone' => $data['phone'],    
            'address' => $data['address'],
        ]);
        $address->save();

        return $address;
    }
    public function updateAddress(Request $request)
    {
        $data = $request->input();

        Address::where('id',  $data['id'])
        ->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'address' => $data['address']
        ]);

        $updatedData = Address::where('id', $data['id'])->first();
        return $updatedData;
    }
    public function deleteAddress(Request $request)
    {
        $data = $request->input();

        // Address::where('id',  $data['id'])->delete();

        // return response()->json([
        //     'status' => true,
        //     'message' => 'Delete address successful'
        // ], 201);
        $orderWithAddress = Order::where('address_id',  $data['id'])->first();

        if ($orderWithAddress) {
            // Nếu id của address đã được sử dụng trong bảng order, cập nhật status của address thành 0
            $address = Address::find( $data['id']);
            if ($address) {
                $address->status = 0;
                $address->save();

                return response()->json(['message' => 'Address status updated to 0']);
            } else {
                return response()->json(['error' => 'Address not found'], 404);
            }
        } else {
            // Nếu id của address không được sử dụng trong bảng order, xóa address
            $address = Address::find( $data['id']);
            if ($address) {
                $address->delete();

                return response()->json(['message' => 'Address deleted']);
            } else {
                return response()->json(['error' => 'Address not found'], 404);
            }
        }
    }
}
