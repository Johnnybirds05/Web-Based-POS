<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $fillable = [
        'product_name',
        'description',
        'category',
        'original_price',
        'retail_price',
        'sub_retail_price',
        'quantity_value',
    ];

    public function transactions(){
        return $this->hasMany(TransactionDetail::class,'product_id','product_id');
    }
    public function change_quantity(){
        return $this->hasMany(ChangeQuantity::class,'product_id','product_id');
    }
    public function change_cost(){
        return $this->hasMany(ChangeCost::class,'product_id','product_id');
    }
}
