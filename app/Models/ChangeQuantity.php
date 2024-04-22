<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeQuantity extends Model
{
    use HasFactory;
    protected $table = 'change_quatity';
    protected $primaryKey = 'alter_quantity_id';
    protected $fillable = [
        'product_id',
        'user_id',
        'quantity',
        'remarks',
    ];
}
