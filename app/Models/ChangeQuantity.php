<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeQuantity extends Model
{
    use HasFactory;
    protected $table = 'change_quantity';
    protected $primaryKey = 'alter_quantity_id';
    protected $fillable = [
        'change_quantity_id',
        'product_id',
        'quantity',
    ];
}
