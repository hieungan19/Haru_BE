<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brands extends Model
{
    use HasFactory;
    protected $table = 'brands'; 

    public $timestamps = false;

    protected $fillable = [
        'name',
        'name_str'
    ];
}
