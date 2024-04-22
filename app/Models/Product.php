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
        'quantity_value',
        'total_quantity',
        'current_quantity',
        'error'
    ];

    public function transaction(){
        return $this->hasMany(Transaction::class,'product_id','product_id');
    }
    public function change_quantity(){
        return $this->hasMany(ChangeQuantity::class,'product_id','product_id');
    }
    public function change_cost(){
        return $this->hasMany(ChangeCost::class,'product_id','product_id');
    }
}
