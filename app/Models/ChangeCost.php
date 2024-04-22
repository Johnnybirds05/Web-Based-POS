<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeCost extends Model
{
    use HasFactory;
    protected $table = 'price';
    protected $primaryKey = 'alter_price_id';
    protected $fillable = [
        'product_id',
        'user_id',
        'alter_original_price',
        'alter_retail_price',
        'remarks',
    ];
}
