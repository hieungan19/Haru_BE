<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $table = 'reviews'; 

    public $timestamps = false;

    protected $fillable = [
        'product_id', 
        'user_id',
        'stars',
        'text',
        'date'
    ]; 
}
