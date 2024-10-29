<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders'; 

    public $timestamps = false;

    protected $fillable = [
        'user_id', 
        'address_id',
        'created_date',
        'delivery_date',
        'total_price',
        'payment_method',
        'payment_status',
        'order_status',
        'evaluated'
    ]; 
}
