<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $table = 'address'; 

    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'name',
        'phone',	
        'address', 
        'status'
    ];
}
